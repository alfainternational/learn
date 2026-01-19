<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reminder extends Model
{
    protected $fillable = [
        'user_id', 'remindable_type', 'remindable_id', 'remind_at',
        'reminder_type', 'message', 'is_sent', 'sent_at'
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function remindable(): MorphTo { return $this->morphTo(); }

    public function scopePending($query) { return $query->where('is_sent', false)->where('remind_at', '<=', now()); }
}
