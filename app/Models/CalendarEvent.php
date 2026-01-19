<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CalendarEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'marketing_plan_id', 'campaign_id', 'title', 'description',
        'event_type', 'start_datetime', 'end_datetime', 'all_day', 'color',
        'location', 'attendees', 'recurrence', 'metadata'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'all_day' => 'boolean',
        'attendees' => 'array',
        'recurrence' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function marketingPlan(): BelongsTo { return $this->belongsTo(MarketingPlan::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function reminders(): MorphMany { return $this->morphMany(Reminder::class, 'remindable'); }

    public function scopeUpcoming($query) { return $query->where('start_datetime', '>=', now()); }
    public function scopeByType($query, $type) { return $query->where('event_type', $type); }
}
