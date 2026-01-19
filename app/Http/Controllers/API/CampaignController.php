<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignMetric;
use App\Services\CampaignAnalysisService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    protected $analysisService;

    public function __construct(CampaignAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    // قائمة الحملات
    public function index(Request $request)
    {
        $campaigns = Campaign::where('user_id', auth()->id())
            ->with(['marketingPlan:id,name', 'metrics' => fn($q) => $q->latest('date')->limit(7)])
            ->when($request->platform, fn($q, $p) => $q->where('platform', $p))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->plan_id, fn($q, $id) => $q->where('marketing_plan_id', $id))
            ->latest()
            ->paginate(20);

        return response()->json($campaigns);
    }

    // إنشاء حملة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|in:facebook,instagram,google,tiktok,snapchat,twitter,linkedin',
            'campaign_type' => 'required|in:awareness,traffic,engagement,leads,conversions,sales',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'planned_budget' => 'required|numeric|min:0',
            'marketing_plan_id' => 'nullable|exists:marketing_plans,id',
            'target_audience' => 'nullable|array',
            'ad_type' => 'nullable|string',
            'ad_copy' => 'nullable|string',
            'cta' => 'nullable|string',
            'landing_page_url' => 'nullable|url',
        ]);

        $campaign = Campaign::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'active',
            'currency' => $request->currency ?? 'USD',
        ]);

        return response()->json(['success' => true, 'data' => $campaign], 201);
    }

    // عرض حملة
    public function show(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        
        $campaign->load(['marketingPlan', 'metrics', 'analyses', 'tasks', 'assets']);
        $campaign->append(['total_metrics', 'budget_usage_percent']);

        return response()->json($campaign);
    }

    // تحديث حملة
    public function update(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $campaign->update($request->validated());
        return response()->json(['success' => true, 'data' => $campaign]);
    }

    // إضافة مؤشرات
    public function addMetrics(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'impressions' => 'nullable|integer|min:0',
            'reach' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
            'spend' => 'nullable|numeric|min:0',
            'revenue' => 'nullable|numeric|min:0',
            'leads' => 'nullable|integer|min:0',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0',
            'video_views' => 'nullable|integer|min:0',
        ]);

        // حساب المؤشرات المشتقة
        $impressions = $validated['impressions'] ?? 0;
        $clicks = $validated['clicks'] ?? 0;
        $spend = $validated['spend'] ?? 0;
        $conversions = $validated['conversions'] ?? 0;
        $revenue = $validated['revenue'] ?? 0;

        $validated['ctr'] = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
        $validated['cpm'] = $impressions > 0 ? ($spend / $impressions) * 1000 : 0;
        $validated['cpc'] = $clicks > 0 ? $spend / $clicks : 0;
        $validated['cpa'] = $conversions > 0 ? $spend / $conversions : 0;
        $validated['conversion_rate'] = $clicks > 0 ? ($conversions / $clicks) * 100 : 0;
        $validated['roas'] = $spend > 0 ? $revenue / $spend : 0;

        $metric = CampaignMetric::updateOrCreate(
            ['campaign_id' => $campaign->id, 'date' => $validated['date']],
            $validated
        );

        // تحديث إجمالي الإنفاق
        $campaign->update(['actual_spend' => $campaign->metrics()->sum('spend')]);

        return response()->json(['success' => true, 'data' => $metric]);
    }

    // تحليل الحملة
    public function analyze(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        
        $analysis = $this->analysisService->analyzeCampaign($campaign);
        
        return response()->json(['success' => true, 'data' => $analysis]);
    }

    // مقارنة مع الخطة
    public function compareWithPlan(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        
        if (!$campaign->marketing_plan_id) {
            return response()->json(['error' => 'لا توجد خطة تسويقية مرتبطة'], 400);
        }
        
        $comparison = $this->analysisService->compareWithPlan($campaign);
        
        return response()->json(['success' => true, 'data' => $comparison]);
    }

    // التوصيات
    public function getRecommendations(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        
        $recommendations = $this->analysisService->generateRecommendations($campaign);
        
        return response()->json(['success' => true, 'data' => $recommendations]);
    }

    // Dashboard إحصائيات
    public function dashboard(Request $request)
    {
        $userId = auth()->id();
        $period = $request->period ?? 30; // أيام
        $startDate = now()->subDays($period);

        $stats = [
            'total_campaigns' => Campaign::where('user_id', $userId)->count(),
            'active_campaigns' => Campaign::where('user_id', $userId)->active()->count(),
            'total_spend' => CampaignMetric::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
                ->where('date', '>=', $startDate)->sum('spend'),
            'total_revenue' => CampaignMetric::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
                ->where('date', '>=', $startDate)->sum('revenue'),
            'total_impressions' => CampaignMetric::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
                ->where('date', '>=', $startDate)->sum('impressions'),
            'total_conversions' => CampaignMetric::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
                ->where('date', '>=', $startDate)->sum('conversions'),
        ];

        $stats['overall_roas'] = $stats['total_spend'] > 0 
            ? round($stats['total_revenue'] / $stats['total_spend'], 2) 
            : 0;

        // الأداء حسب المنصة
        $stats['by_platform'] = Campaign::where('user_id', $userId)
            ->selectRaw('platform, COUNT(*) as count, SUM(actual_spend) as spend')
            ->groupBy('platform')
            ->get();

        // أفضل الحملات
        $stats['top_campaigns'] = Campaign::where('user_id', $userId)
            ->with(['metrics' => fn($q) => $q->selectRaw('campaign_id, SUM(revenue) as total_revenue, SUM(spend) as total_spend')
                ->groupBy('campaign_id')])
            ->limit(5)
            ->get();

        return response()->json($stats);
    }
}
