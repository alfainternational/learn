<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignMetric;
use App\Models\AnalyticsSnapshot;
use App\Models\MarketingPlan;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $userId = auth()->id();
        $period = $request->period ?? 30;
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // إحصائيات عامة
        $stats = $this->getGeneralStats($userId, $startDate, $endDate);
        
        // الأداء عبر الوقت
        $timeSeries = $this->getTimeSeries($userId, $startDate, $endDate);
        
        // الأداء حسب المنصة
        $byPlatform = $this->getByPlatform($userId, $startDate, $endDate);
        
        // أفضل الحملات
        $topCampaigns = $this->getTopCampaigns($userId, $startDate, $endDate);

        return response()->json([
            'stats' => $stats,
            'time_series' => $timeSeries,
            'by_platform' => $byPlatform,
            'top_campaigns' => $topCampaigns,
            'period' => ['start' => $startDate, 'end' => $endDate],
        ]);
    }

    protected function getGeneralStats($userId, $startDate, $endDate)
    {
        $metrics = CampaignMetric::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                SUM(impressions) as impressions,
                SUM(reach) as reach,
                SUM(clicks) as clicks,
                SUM(conversions) as conversions,
                SUM(leads) as leads,
                SUM(spend) as spend,
                SUM(revenue) as revenue
            ')
            ->first();

        // الفترة السابقة للمقارنة
        $previousStart = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousMetrics = CampaignMetric::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('date', [$previousStart, $startDate])
            ->selectRaw('SUM(spend) as spend, SUM(revenue) as revenue, SUM(conversions) as conversions')
            ->first();

        return [
            'total_spend' => $metrics->spend ?? 0,
            'total_revenue' => $metrics->revenue ?? 0,
            'total_impressions' => $metrics->impressions ?? 0,
            'total_clicks' => $metrics->clicks ?? 0,
            'total_conversions' => $metrics->conversions ?? 0,
            'total_leads' => $metrics->leads ?? 0,
            'roas' => $metrics->spend > 0 ? round($metrics->revenue / $metrics->spend, 2) : 0,
            'ctr' => $metrics->impressions > 0 ? round(($metrics->clicks / $metrics->impressions) * 100, 2) : 0,
            'conversion_rate' => $metrics->clicks > 0 ? round(($metrics->conversions / $metrics->clicks) * 100, 2) : 0,
            'active_campaigns' => Campaign::where('user_id', $userId)->active()->count(),
            'total_plans' => MarketingPlan::where('user_id', $userId)->count(),
            'pending_tasks' => Task::where('user_id', $userId)->pending()->count(),
            
            // التغيير عن الفترة السابقة
            'spend_change' => $this->calculateChange($metrics->spend, $previousMetrics->spend),
            'revenue_change' => $this->calculateChange($metrics->revenue, $previousMetrics->revenue),
            'conversions_change' => $this->calculateChange($metrics->conversions, $previousMetrics->conversions),
        ];
    }

    protected function getTimeSeries($userId, $startDate, $endDate)
    {
        return CampaignMetric::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(spend) as spend, SUM(revenue) as revenue, SUM(impressions) as impressions, SUM(conversions) as conversions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function getByPlatform($userId, $startDate, $endDate)
    {
        return Campaign::where('user_id', $userId)
            ->with(['metrics' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('campaign_id, SUM(spend) as spend, SUM(revenue) as revenue, SUM(conversions) as conversions')
                ->groupBy('campaign_id')])
            ->get()
            ->groupBy('platform')
            ->map(function($campaigns) {
                return [
                    'count' => $campaigns->count(),
                    'spend' => $campaigns->sum(fn($c) => $c->metrics->sum('spend')),
                    'revenue' => $campaigns->sum(fn($c) => $c->metrics->sum('revenue')),
                    'conversions' => $campaigns->sum(fn($c) => $c->metrics->sum('conversions')),
                ];
            });
    }

    protected function getTopCampaigns($userId, $startDate, $endDate, $limit = 5)
    {
        return Campaign::where('user_id', $userId)
            ->with(['metrics' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])])
            ->get()
            ->map(function($campaign) {
                $metrics = $campaign->metrics;
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'platform' => $campaign->platform,
                    'spend' => $metrics->sum('spend'),
                    'revenue' => $metrics->sum('revenue'),
                    'roas' => $metrics->sum('spend') > 0 ? round($metrics->sum('revenue') / $metrics->sum('spend'), 2) : 0,
                    'conversions' => $metrics->sum('conversions'),
                ];
            })
            ->sortByDesc('revenue')
            ->take($limit)
            ->values();
    }

    protected function calculateChange($current, $previous)
    {
        if (!$previous || $previous == 0) return 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    // تقرير مقارنة الحملات
    public function compareCampaigns(Request $request)
    {
        $campaignIds = $request->campaign_ids;
        
        $campaigns = Campaign::whereIn('id', $campaignIds)
            ->where('user_id', auth()->id())
            ->with('metrics')
            ->get()
            ->map(function($campaign) {
                $metrics = $campaign->metrics;
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'platform' => $campaign->platform,
                    'type' => $campaign->campaign_type,
                    'budget' => $campaign->planned_budget,
                    'spend' => $metrics->sum('spend'),
                    'revenue' => $metrics->sum('revenue'),
                    'impressions' => $metrics->sum('impressions'),
                    'clicks' => $metrics->sum('clicks'),
                    'conversions' => $metrics->sum('conversions'),
                    'ctr' => $metrics->sum('impressions') > 0 ? round(($metrics->sum('clicks') / $metrics->sum('impressions')) * 100, 2) : 0,
                    'roas' => $metrics->sum('spend') > 0 ? round($metrics->sum('revenue') / $metrics->sum('spend'), 2) : 0,
                ];
            });

        return response()->json($campaigns);
    }

    // تقرير مخصص
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:campaign,period,comparison,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'campaign_ids' => 'nullable|array',
            'metrics' => 'nullable|array',
        ]);

        $data = $this->dashboard($request);

        $report = \App\Models\Report::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'filters' => $validated,
            'data' => $data->getData(),
        ]);

        return response()->json(['success' => true, 'data' => $report]);
    }

    public function reports()
    {
        $reports = \App\Models\Report::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return response()->json($reports);
    }
}
