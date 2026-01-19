<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignAnalysis extends Model
{
    protected $fillable = [
        'campaign_id', 'user_id', 'analysis_type', 'plan_comparison',
        'strengths', 'weaknesses', 'recommendations', 'benchmarks',
        'overall_score', 'ai_summary', 'ai_insights'
    ];

    protected $casts = [
        'plan_comparison' => 'array',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'recommendations' => 'array',
        'benchmarks' => 'array',
        'ai_insights' => 'array',
        'overall_score' => 'decimal:2',
    ];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
