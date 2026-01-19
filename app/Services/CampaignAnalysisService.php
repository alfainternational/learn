<?php
namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignAnalysis;
use App\Models\MarketingPlan;
use App\Services\AI\GeminiService;

class CampaignAnalysisService
{
    protected $geminiService;

    // Industry benchmarks
    protected $benchmarks = [
        'facebook' => ['ctr' => 0.9, 'cpc' => 1.72, 'cpm' => 11.54, 'conversion_rate' => 9.21],
        'instagram' => ['ctr' => 0.58, 'cpc' => 1.23, 'cpm' => 7.91, 'conversion_rate' => 1.08],
        'google' => ['ctr' => 3.17, 'cpc' => 2.69, 'cpm' => 38.40, 'conversion_rate' => 4.40],
        'tiktok' => ['ctr' => 0.84, 'cpc' => 1.00, 'cpm' => 10.00, 'conversion_rate' => 1.50],
        'linkedin' => ['ctr' => 0.44, 'cpc' => 5.26, 'cpm' => 33.80, 'conversion_rate' => 2.74],
    ];

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function analyzeCampaign(Campaign $campaign): CampaignAnalysis
    {
        $metrics = $campaign->total_metrics;
        $benchmarks = $this->getBenchmarks($campaign->platform);
        
        $strengths = [];
        $weaknesses = [];
        $recommendations = [];

        // تحليل CTR
        if ($metrics->ctr >= $benchmarks['ctr']) {
            $strengths[] = [
                'metric' => 'CTR',
                'value' => $metrics->ctr,
                'benchmark' => $benchmarks['ctr'],
                'message' => 'معدل النقر أعلى من المتوسط'
            ];
        } else {
            $weaknesses[] = [
                'metric' => 'CTR',
                'value' => $metrics->ctr,
                'benchmark' => $benchmarks['ctr'],
                'message' => 'معدل النقر أقل من المتوسط'
            ];
            $recommendations[] = [
                'priority' => 'high',
                'area' => 'creative',
                'action' => 'جرب عناوين أكثر جذباً وصور مختلفة',
                'expected_impact' => 'تحسين CTR بنسبة 20-30%'
            ];
        }

        // تحليل CPC
        if ($metrics->cpc <= $benchmarks['cpc']) {
            $strengths[] = [
                'metric' => 'CPC',
                'value' => $metrics->cpc,
                'benchmark' => $benchmarks['cpc'],
                'message' => 'تكلفة النقرة أقل من المتوسط'
            ];
        } else {
            $weaknesses[] = [
                'metric' => 'CPC',
                'value' => $metrics->cpc,
                'benchmark' => $benchmarks['cpc'],
                'message' => 'تكلفة النقرة أعلى من المتوسط'
            ];
            $recommendations[] = [
                'priority' => 'medium',
                'area' => 'targeting',
                'action' => 'راجع الاستهداف وضيق الجمهور',
                'expected_impact' => 'تقليل CPC بنسبة 15-25%'
            ];
        }

        // تحليل ROAS
        if ($metrics->roas >= 2) {
            $strengths[] = [
                'metric' => 'ROAS',
                'value' => $metrics->roas,
                'benchmark' => 2,
                'message' => 'عائد الإنفاق الإعلاني جيد'
            ];
        } else {
            $weaknesses[] = [
                'metric' => 'ROAS',
                'value' => $metrics->roas,
                'benchmark' => 2,
                'message' => 'عائد الإنفاق الإعلاني ضعيف'
            ];
            $recommendations[] = [
                'priority' => 'high',
                'area' => 'conversion',
                'action' => 'حسن صفحة الهبوط ورحلة الشراء',
                'expected_impact' => 'زيادة ROAS بنسبة 30-50%'
            ];
        }

        // تحليل الميزانية
        $budgetUsage = $campaign->budget_usage_percent;
        if ($budgetUsage > 100) {
            $weaknesses[] = [
                'metric' => 'budget',
                'value' => $budgetUsage,
                'message' => 'تجاوزت الميزانية المخططة'
            ];
        }

        // حساب الدرجة الإجمالية
        $score = $this->calculateScore($metrics, $benchmarks);

        // إنشاء التحليل
        $analysis = CampaignAnalysis::create([
            'campaign_id' => $campaign->id,
            'user_id' => auth()->id(),
            'analysis_type' => 'performance',
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'recommendations' => $recommendations,
            'benchmarks' => $benchmarks,
            'overall_score' => $score,
        ]);

        return $analysis;
    }

    public function compareWithPlan(Campaign $campaign): array
    {
        $plan = $campaign->marketingPlan;
        if (!$plan) return ['error' => 'لا توجد خطة'];

        $planSections = $plan->sections;
        $comparison = [];

        // مقارنة الجمهور المستهدف
        $plannedAudience = $planSections->where('section_type', 'target_audience')->first();
        if ($plannedAudience && $campaign->target_audience) {
            $comparison['audience'] = [
                'planned' => $plannedAudience->section_data,
                'actual' => $campaign->target_audience,
                'match_score' => $this->calculateAudienceMatch($plannedAudience->section_data, $campaign->target_audience)
            ];
        }

        // مقارنة الميزانية
        $plannedBudget = $planSections->where('section_type', 'budget')->first();
        if ($plannedBudget) {
            $comparison['budget'] = [
                'planned' => $campaign->planned_budget,
                'actual' => $campaign->actual_spend,
                'variance' => $campaign->actual_spend - $campaign->planned_budget,
                'variance_percent' => $campaign->planned_budget > 0 
                    ? (($campaign->actual_spend - $campaign->planned_budget) / $campaign->planned_budget) * 100 
                    : 0
            ];
        }

        // مقارنة القنوات
        $plannedChannels = $planSections->where('section_type', 'marketing_channels')->first();
        if ($plannedChannels) {
            $comparison['channels'] = [
                'planned' => $plannedChannels->section_data,
                'actual' => $campaign->platform,
                'is_aligned' => $this->isChannelAligned($plannedChannels->section_data, $campaign->platform)
            ];
        }

        return $comparison;
    }

    public function generateRecommendations(Campaign $campaign): array
    {
        $metrics = $campaign->total_metrics;
        $recommendations = [];

        // توصيات بناء على الأداء
        if ($metrics->ctr < 1) {
            $recommendations[] = [
                'category' => 'المحتوى الإعلاني',
                'priority' => 'high',
                'title' => 'تحسين العنوان والصورة',
                'description' => 'معدل النقر منخفض. جرب عناوين أكثر إثارة وصور ملفتة.',
                'actions' => [
                    'استخدم أرقام وإحصائيات في العنوان',
                    'جرب صور بوجوه بشرية',
                    'أضف عنصر الإلحاح (Urgency)'
                ]
            ];
        }

        if ($metrics->conversion_rate < 2) {
            $recommendations[] = [
                'category' => 'صفحة الهبوط',
                'priority' => 'high',
                'title' => 'تحسين صفحة الهبوط',
                'description' => 'معدل التحويل منخفض. المشكلة غالباً في صفحة الهبوط.',
                'actions' => [
                    'تأكد من تطابق الرسالة بين الإعلان والصفحة',
                    'بسط نموذج التسجيل',
                    'أضف شهادات العملاء',
                    'حسن سرعة الصفحة'
                ]
            ];
        }

        if ($metrics->roas < 2) {
            $recommendations[] = [
                'category' => 'الاستهداف',
                'priority' => 'medium',
                'title' => 'تحسين الاستهداف',
                'description' => 'عائد الإنفاق ضعيف. راجع جمهورك المستهدف.',
                'actions' => [
                    'استخدم Lookalike audiences',
                    'استهدف العملاء الحاليين (Retargeting)',
                    'ضيق الاهتمامات'
                ]
            ];
        }

        return $recommendations;
    }

    protected function getBenchmarks(string $platform): array
    {
        return $this->benchmarks[$platform] ?? $this->benchmarks['facebook'];
    }

    protected function calculateScore($metrics, $benchmarks): float
    {
        $scores = [];
        
        // CTR score (25%)
        $scores[] = min(($metrics->ctr / $benchmarks['ctr']) * 25, 25);
        
        // CPC score (25%) - lower is better
        $scores[] = $metrics->cpc > 0 ? min(($benchmarks['cpc'] / $metrics->cpc) * 25, 25) : 0;
        
        // Conversion rate score (25%)
        $scores[] = min(($metrics->conversion_rate / $benchmarks['conversion_rate']) * 25, 25);
        
        // ROAS score (25%)
        $scores[] = min(($metrics->roas / 2) * 25, 25);

        return round(array_sum($scores), 2);
    }

    protected function calculateAudienceMatch($planned, $actual): int
    {
        // حساب نسبة التطابق
        return 75; // مبسط - يمكن تطويره
    }

    protected function isChannelAligned($planned, $actual): bool
    {
        return true; // مبسط
    }
}
