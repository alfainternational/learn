<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'section_type',
        'section_data',
        'ai_suggestions',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'section_data' => 'array',
        'ai_suggestions' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public const SECTION_TYPES = [
        'personal_card' => 'بطاقة المشارك',
        'diagnosis' => 'التشخيص',
        'target_audience' => 'الشريحة المستهدفة',
        'core_message' => 'الرسالة الأساسية',
        'offer_stack' => 'العرض المتكامل',
        'channels' => 'القنوات التسويقية',
        'funnel' => 'مسار التحويل',
        'followup' => 'المتابعة التلقائية',
        'metrics' => 'المؤشرات',
        'competitor_analysis' => 'تحليل المنافسين',
        'content_plan' => 'خطة المحتوى',
        'budget_breakdown' => 'تفصيل الميزانية',
    ];

    public function plan()
    {
        return $this->belongsTo(MarketingPlan::class, 'plan_id');
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $this->plan->updateCompletionPercentage();
    }

    public function addAISuggestions(array $suggestions): void
    {
        $this->update([
            'ai_suggestions' => $suggestions,
        ]);
    }

    public function getSectionTypeName(): string
    {
        return self::SECTION_TYPES[$this->section_type] ?? $this->section_type;
    }
}
