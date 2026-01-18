<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdImpression extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'campaign_id', 'user_id', 'placement', 'action',
        'ip_address', 'user_agent', 'referrer', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($impression) {
            $impression->created_at = now();
        });
    }

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
