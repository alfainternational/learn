<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tier',
        'status',
        'amount',
        'currency',
        'billing_cycle',
        'starts_at',
        'expires_at',
        'auto_renew',
        'stripe_subscription_id',
        'moyasar_subscription_id',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'auto_renew' => false,
        ]);

        $this->user->update([
            'subscription_tier' => 'free',
            'subscription_expires_at' => null,
        ]);
    }

    public function renew(): void
    {
        $newExpiresAt = match($this->billing_cycle) {
            'monthly' => $this->expires_at->addMonth(),
            'yearly' => $this->expires_at->addYear(),
        };

        $this->update([
            'expires_at' => $newExpiresAt,
            'status' => 'active',
        ]);
    }

    public function daysUntilExpiry(): int
    {
        return now()->diffInDays($this->expires_at, false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }
}
