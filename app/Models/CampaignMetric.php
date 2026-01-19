<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignMetric extends Model
{
    protected $fillable = [
        'campaign_id', 'date', 'impressions', 'reach', 'frequency',
        'clicks', 'ctr', 'likes', 'comments', 'shares', 'saves',
        'conversions', 'conversion_rate', 'leads', 'purchases', 'revenue',
        'spend', 'cpm', 'cpc', 'cpl', 'cpa', 'roas',
        'video_views', 'video_views_25', 'video_views_50', 'video_views_75', 'video_views_100'
    ];

    protected $casts = [
        'date' => 'date',
        'ctr' => 'decimal:4',
        'conversion_rate' => 'decimal:4',
        'spend' => 'decimal:2',
        'revenue' => 'decimal:2',
        'cpm' => 'decimal:4',
        'cpc' => 'decimal:4',
        'cpl' => 'decimal:4',
        'cpa' => 'decimal:4',
        'roas' => 'decimal:4',
    ];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
}
