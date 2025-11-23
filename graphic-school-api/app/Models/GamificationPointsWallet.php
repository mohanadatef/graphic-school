<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationPointsWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'level_id',
    ];

    protected $casts = [
        'total_points' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function level()
    {
        return $this->belongsTo(GamificationLevel::class, 'level_id');
    }

    public function incrementPoints(int $points): void
    {
        $this->increment('total_points', $points);
        $this->refresh();
    }
}

