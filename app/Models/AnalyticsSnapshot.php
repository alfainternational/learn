<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsSnapshot extends Model
{
    protected $fillable = [
        'user_id', 'marketing_plan_id', 'snapshot_date', 'period_type',
        'total_campaigns', 'active_campaigns', 'total_spend', 'total_revenue',
        'total_impressions', 'total_clicks', 'total_conversions',
        'avg_ctr', 'avg_cpc', 'avg_roas', 'platform_breakdown', 'top_campaigns'
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'total_spend' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'avg_ctr' => 'decimal:4',
        'avg_cpc' => 'decimal:4',
        'avg_roas' => 'decimal:4',
        'platform_breakdown' => 'array',
        'top_campaigns' => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function marketingPlan(): BelongsTo { return $this->belongsTo(MarketingPlan::class); }

    public function scopeByPeriod($query, $period) { return $query->where('period_type', $period); }
}
