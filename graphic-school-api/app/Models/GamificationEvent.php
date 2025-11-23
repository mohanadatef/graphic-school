<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rule_id',
        'event_type',
        'reference_table',
        'reference_id',
        'points_awarded',
        'meta',
    ];

    protected $casts = [
        'points_awarded' => 'integer',
        'meta' => 'array',
        'reference_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rule()
    {
        return $this->belongsTo(GamificationRule::class, 'rule_id');
    }
}

