<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_points',
        'max_points',
        'icon',
        'color',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
    ];

    public function wallets()
    {
        return $this->hasMany(GamificationPointsWallet::class, 'level_id');
    }

    public static function getLevelForPoints(int $points): ?self
    {
        return static::where('min_points', '<=', $points)
            ->where(function ($query) use ($points) {
                $query->whereNull('max_points')
                    ->orWhere('max_points', '>=', $points);
            })
            ->orderBy('min_points', 'desc')
            ->first();
    }
}

