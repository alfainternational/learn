<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MarketingPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'status',
        'year',
        'business_name',
        'industry',
        'target_audience',
        'marketing_goal',
        'budget_monthly',
        'completion_percentage',
        'ai_score',
        'last_ai_review_at',
        'sections_completed',
        'is_public',
        'share_token',
        'view_count',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'budget_monthly' => 'decimal:2',
        'sections_completed' => 'array',
        'last_ai_review_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->title)) {
                $plan->title = "خطة تسويقية {$plan->year}";
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sections()
    {
        return $this->hasMany(PlanSection::class, 'plan_id');
    }

    public function aiConversations()
    {
        return $this->hasMany(AIConversation::class, 'plan_id');
    }

    // Methods
    public function generateShareToken(): string
    {
        $this->share_token = Str::random(64);
        $this->save();
        return $this->share_token;
    }

    public function revokeShareToken(): void
    {
        $this->update([
            'share_token' => null,
            'is_public' => false,
        ]);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function updateCompletionPercentage(): void
    {
        $totalSections = 12;
        $completedSections = $this->sections()->where('is_completed', true)->count();

        $this->completion_percentage = (int) (($completedSections / $totalSections) * 100);
        $this->save();
    }

    public function getSectionData(string $sectionType): ?array
    {
        $section = $this->sections()->where('section_type', $sectionType)->first();
        return $section?->section_data;
    }

    public function updateSectionData(string $sectionType, array $data, bool $isCompleted = false): PlanSection
    {
        return $this->sections()->updateOrCreate(
            ['section_type' => $sectionType],
            [
                'section_data' => $data,
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
            ]
        );
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completion_percentage', 100);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
