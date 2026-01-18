<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_url',
        'role',
        'subscription_tier',
        'subscription_expires_at',
        'ai_credits_remaining',
        'ai_credits_reset_at',
        'onboarding_completed',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'ai_credits_reset_at' => 'datetime',
        'last_login_at' => 'datetime',
        'onboarding_completed' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function marketingPlans()
    {
        return $this->hasMany(MarketingPlan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function aiConversations()
    {
        return $this->hasMany(AIConversation::class);
    }

    public function adCampaigns()
    {
        return $this->hasMany(AdCampaign::class, 'advertiser_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Subscription Methods
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_tier !== 'free'
            && $this->subscription_expires_at?->isFuture();
    }

    public function canCreatePlan(): bool
    {
        $maxPlans = match($this->subscription_tier) {
            'free' => 1,
            'basic' => 3,
            'pro', 'enterprise' => PHP_INT_MAX,
            default => 0,
        };

        return $this->marketingPlans()->count() < $maxPlans;
    }

    public function hasAICredits(): bool
    {
        if ($this->subscription_tier !== 'free') {
            return true;
        }

        $this->resetAICreditsIfNeeded();
        return $this->ai_credits_remaining > 0;
    }

    public function deductAICredit(): void
    {
        if ($this->subscription_tier === 'free') {
            $this->decrement('ai_credits_remaining');
        }
    }

    private function resetAICreditsIfNeeded(): void
    {
        if (!$this->ai_credits_reset_at || $this->ai_credits_reset_at->isPast()) {
            $this->update([
                'ai_credits_remaining' => 3,
                'ai_credits_reset_at' => now()->addMonth(),
            ]);
        }
    }

    // Role Methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdvertiser(): bool
    {
        return $this->role === 'advertiser';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->where('subscription_tier', '!=', 'free')
            ->where('subscription_expires_at', '>', now());
    }
}
