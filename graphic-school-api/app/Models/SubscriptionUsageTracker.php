<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUsageTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'key',
        'used',
        'limit',
    ];

    protected $casts = [
        'used' => 'integer',
        'limit' => 'integer',
    ];

    public function academy()
    {
        return $this->belongsTo(\Modules\ACL\Users\Models\User::class, 'academy_id');
    }

    public function isExceeded(): bool
    {
        return $this->used >= $this->limit && $this->limit > 0;
    }

    public function getUsagePercentage(): float
    {
        if ($this->limit <= 0) {
            return 0;
        }
        return min(100, ($this->used / $this->limit) * 100);
    }

    public function incrementUsage(int $amount = 1): void
    {
        $this->increment('used', $amount);
    }

    public function decrementUsage(int $amount = 1): void
    {
        $this->decrement('used', $amount);
    }
}

