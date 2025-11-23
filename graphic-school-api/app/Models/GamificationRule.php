<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'points',
        'max_times_per_period',
        'scope',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'max_times_per_period' => 'integer',
        'points' => 'integer',
    ];

    public function events()
    {
        return $this->hasMany(GamificationEvent::class, 'rule_id');
    }
}

