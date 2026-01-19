<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'campaign_id', 'marketing_plan_id', 'name', 'type',
        'file_path', 'file_url', 'file_size', 'mime_type', 'dimensions',
        'metadata', 'tags', 'folder'
    ];

    protected $casts = [
        'dimensions' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function marketingPlan(): BelongsTo { return $this->belongsTo(MarketingPlan::class); }

    public function scopeByType($query, $type) { return $query->where('type', $type); }
    public function scopeInFolder($query, $folder) { return $query->where('folder', $folder); }
}
