<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'condition_type',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function userBadges()
    {
        return $this->hasMany(GamificationUserBadge::class, 'badge_id');
    }
}

