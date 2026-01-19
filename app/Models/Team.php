<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id', 'name', 'description', 'logo', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function members(): HasMany { return $this->hasMany(TeamMember::class); }
    public function clients(): HasMany { return $this->hasMany(AgencyClient::class); }
}
