<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'industry', 'template_data', 'thumbnail_url',
        'is_premium', 'price', 'usage_count', 'rating', 'created_by',
        'is_featured', 'is_active',
    ];

    protected $casts = [
        'template_data' => 'array',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeByIndustry($query, string $industry)
    {
        return $query->where('industry', $industry);
    }
}
