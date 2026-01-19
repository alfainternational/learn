<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'marketing_plan_id', 'campaign_id', 'assigned_to',
        'title', 'description', 'status', 'priority', 'category',
        'due_date', 'due_time', 'completed_at', 'checklist', 'attachments', 'tags', 'order'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'checklist' => 'array',
        'attachments' => 'array',
        'tags' => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function marketingPlan(): BelongsTo { return $this->belongsTo(MarketingPlan::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function comments(): HasMany { return $this->hasMany(TaskComment::class); }
    public function reminders(): MorphMany { return $this->morphMany(Reminder::class, 'remindable'); }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeOverdue($query) { return $query->where('due_date', '<', now())->whereNull('completed_at'); }
    public function scopeByPriority($query, $priority) { return $query->where('priority', $priority); }
}
