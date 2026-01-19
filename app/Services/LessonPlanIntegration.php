<?php

namespace App\Services;

class LessonPlanIntegration
{
    /**
     * خريطة الربط بين أقسام الخطة والدروس والأدوات
     */
    protected array $integrationMap = [
        'target_audience' => [
            'lessons' => [
                ['id' => 3, 'title' => 'أعرف منو هو عميلك فعلاً', 'description' => 'تعرف على خصائص العميل المثالي'],
                ['id' => 4, 'title' => 'رحلة البحث عن مواصفات العميل المثالي', 'description' => 'كيف تبحث وتحدد عميلك'],
            ],
            'tools' => [
                ['slug' => 'persona-builder', 'name' => 'PersonaBuilder', 'icon' => 'users'],
            ],
        ],
        'marketing_channels' => [
            'lessons' => [
                ['id' => 7, 'title' => 'القنوات التسويقية', 'description' => 'اختر القنوات المناسبة لعملك'],
            ],
            'tools' => [
                ['slug' => 'channel-comparison', 'name' => 'ChannelComparison', 'icon' => 'share-2'],
            ],
        ],
        'channels' => [
            'lessons' => [
                ['id' => 7, 'title' => 'القنوات التسويقية', 'description' => 'اختر القنوات المناسبة لعملك'],
            ],
            'tools' => [
                ['slug' => 'channel-comparison', 'name' => 'ChannelComparison', 'icon' => 'share-2'],
            ],
        ],
        'content_plan' => [
            'lessons' => [
                ['id' => 8, 'title' => 'خطة المحتوى التسويقي', 'description' => 'بناء خطة محتوى فعالة'],
                ['id' => 10, 'title' => 'كيف تبني محتوى يبيع', 'description' => 'أسرار المحتوى البيعي'],
            ],
            'tools' => [
                ['slug' => 'content-calendar', 'name' => 'ContentCalendar', 'icon' => 'calendar'],
            ],
        ],
        'budget' => [
            'lessons' => [
                ['id' => 11, 'title' => 'الإعلان الممول', 'description' => 'إدارة ميزانية الإعلانات بذكاء'],
            ],
            'tools' => [
                ['slug' => 'ad-budget-calculator', 'name' => 'AdBudgetCalculator', 'icon' => 'dollar-sign'],
            ],
        ],
        'kpis' => [
            'lessons' => [
                ['id' => 13, 'title' => 'قياس النتائج التسويقية والتحليل', 'description' => 'كيف تقيس نجاح حملاتك'],
            ],
            'tools' => [
                ['slug' => 'roi-calculator', 'name' => 'ROICalculator', 'icon' => 'trending-up'],
            ],
        ],
        'diagnosis' => [
            'lessons' => [
                ['id' => 2, 'title' => 'افهم السوق قبل تبدأ', 'description' => 'تحليل السوق والمنافسين'],
            ],
            'tools' => [
                ['slug' => 'swot-analysis', 'name' => 'SWOTAnalysis', 'icon' => 'grid'],
            ],
        ],
        'core_message' => [
            'lessons' => [
                ['id' => 5, 'title' => 'صوتك في السوق', 'description' => 'بناء رسالتك التسويقية'],
                ['id' => 6, 'title' => 'كتابة المحتوى التسويقي', 'description' => 'فن كتابة الإعلانات'],
            ],
            'tools' => [
                ['slug' => 'message-builder', 'name' => 'MessageBuilder', 'icon' => 'message-circle'],
            ],
        ],
        'offer_stack' => [
            'lessons' => [
                ['id' => 12, 'title' => 'استراتيجيات زيادة المبيعات', 'description' => 'بناء عروض لا تقاوم'],
            ],
            'tools' => [
                ['slug' => 'offer-builder', 'name' => 'OfferBuilder', 'icon' => 'gift'],
            ],
        ],
    ];

    /**
     * الحصول على الدروس المقترحة لقسم معين
     */
    public function getSuggestedLessons(string $sectionType): array
    {
        return $this->integrationMap[$sectionType]['lessons'] ?? [];
    }

    /**
     * الحصول على الأدوات المرتبطة بقسم معين
     */
    public function getRelatedTools(string $sectionType): array
    {
        return $this->integrationMap[$sectionType]['tools'] ?? [];
    }

    /**
     * الحصول على كل المعلومات المتعلقة بقسم
     */
    public function getSectionIntegration(string $sectionType): array
    {
        return [
            'section_type' => $sectionType,
            'lessons' => $this->getSuggestedLessons($sectionType),
            'tools' => $this->getRelatedTools($sectionType),
        ];
    }

    /**
     * الحصول على جميع الأقسام مع روابطها
     */
    public function getAllIntegrations(): array
    {
        $result = [];
        foreach (array_keys($this->integrationMap) as $sectionType) {
            $result[$sectionType] = $this->getSectionIntegration($sectionType);
        }
        return $result;
    }
}
