<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id', 'user_id', 'company_name', 'contact_name', 'contact_email',
        'contact_phone', 'industry', 'website', 'logo', 'notes', 'status', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function team(): BelongsTo { return $this->belongsTo(Team::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function marketingPlans(): HasMany { return $this->hasMany(MarketingPlan::class, 'client_id'); }
    public function reports(): HasMany { return $this->hasMany(Report::class, 'client_id'); }

    public function scopeActive($query) { return $query->where('status', 'active'); }
}
