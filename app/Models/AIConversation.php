<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIConversation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'plan_id',
        'session_id',
        'message_type',
        'message_text',
        'context',
        'ai_model',
        'tokens_used',
        'processing_time_ms',
        'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conversation) {
            $conversation->created_at = now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(MarketingPlan::class, 'plan_id');
    }

    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc');
    }

    public function scopeUserMessages($query)
    {
        return $query->where('message_type', 'user');
    }

    public function scopeAssistantMessages($query)
    {
        return $query->where('message_type', 'assistant');
    }
}
