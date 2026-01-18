<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertiser_id', 'campaign_name', 'status', 'ad_type', 'title',
        'description', 'image_url', 'cta_text', 'cta_url',
        'target_subscription_tiers', 'target_industries', 'target_locations',
        'ad_placements', 'budget_total', 'budget_spent', 'cost_per_click',
        'cost_per_impression', 'starts_at', 'ends_at', 'impressions',
        'clicks', 'conversions', 'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected $casts = [
        'target_subscription_tiers' => 'array',
        'target_industries' => 'array',
        'target_locations' => 'array',
        'ad_placements' => 'array',
        'budget_total' => 'decimal:2',
        'budget_spent' => 'decimal:2',
        'cost_per_click' => 'decimal:2',
        'cost_per_impression' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function impressions()
    {
        return $this->hasMany(AdImpression::class, 'campaign_id');
    }

    public function getCTR(): float
    {
        return $this->impressions > 0
            ? round(($this->clicks / $this->impressions) * 100, 2)
            : 0;
    }

    public function getRemainingBudget(): float
    {
        return $this->budget_total - $this->budget_spent;
    }

    public function hasBudgetRemaining(): bool
    {
        return $this->budget_spent < $this->budget_total;
    }

    public function approve(User $admin): void
    {
        $this->update([
            'status' => 'active',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
    }

    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereColumn('budget_spent', '<', 'budget_total');
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }
}
