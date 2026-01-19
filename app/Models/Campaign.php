<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'marketing_plan_id', 'name', 'platform', 'campaign_type',
        'objective', 'start_date', 'end_date', 'status', 'planned_budget',
        'actual_spend', 'currency', 'target_audience', 'targeting_settings',
        'ad_type', 'ad_copy', 'cta', 'creative_assets', 'landing_page_url',
        'tracking_url', 'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'planned_budget' => 'decimal:2',
        'actual_spend' => 'decimal:2',
        'target_audience' => 'array',
        'targeting_settings' => 'array',
        'creative_assets' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function marketingPlan(): BelongsTo { return $this->belongsTo(MarketingPlan::class); }
    public function metrics(): HasMany { return $this->hasMany(CampaignMetric::class); }
    public function analyses(): HasMany { return $this->hasMany(CampaignAnalysis::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function assets(): HasMany { return $this->hasMany(Asset::class); }
    public function calendarEvents(): HasMany { return $this->hasMany(CalendarEvent::class); }

    // Accessors
    public function getLatestMetricsAttribute() {
        return $this->metrics()->latest('date')->first();
    }

    public function getTotalMetricsAttribute() {
        return $this->metrics()->selectRaw('
            SUM(impressions) as impressions,
            SUM(reach) as reach,
            SUM(clicks) as clicks,
            SUM(conversions) as conversions,
            SUM(spend) as spend,
            SUM(revenue) as revenue,
            CASE WHEN SUM(impressions) > 0 THEN SUM(clicks) / SUM(impressions) * 100 ELSE 0 END as ctr,
            CASE WHEN SUM(spend) > 0 THEN SUM(revenue) / SUM(spend) ELSE 0 END as roas
        ')->first();
    }

    public function getBudgetUsagePercentAttribute() {
        if ($this->planned_budget <= 0) return 0;
        return round(($this->actual_spend / $this->planned_budget) * 100, 2);
    }

    // Scopes
    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeByPlatform($query, $platform) { return $query->where('platform', $platform); }
    public function scopeInDateRange($query, $start, $end) {
        return $query->where('start_date', '>=', $start)->where(function($q) use ($end) {
            $q->whereNull('end_date')->orWhere('end_date', '<=', $end);
        });
    }
}
