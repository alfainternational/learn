<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'client_id', 'marketing_plan_id', 'name', 'report_type',
        'date_range_start', 'date_range_end', 'sections', 'data',
        'file_path', 'status', 'generated_at', 'scheduled_at', 'schedule_frequency'
    ];

    protected $casts = [
        'date_range_start' => 'date',
        'date_range_end' => 'date',
        'sections' => 'array',
        'data' => 'array',
        'generated_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function client(): BelongsTo { return $this->belongsTo(AgencyClient::class, 'client_id'); }
    public function marketingPlan(): BelongsTo { return $this->belongsTo(MarketingPlan::class); }

    public function scopeByType($query, $type) { return $query->where('report_type', $type); }
    public function scopeGenerated($query) { return $query->where('status', 'generated'); }
}
