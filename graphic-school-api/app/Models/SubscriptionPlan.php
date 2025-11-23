<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'price_monthly',
        'price_yearly',
        'currency',
        'description',
        'features',
        'limits',
        'is_active',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(AcademySubscription::class, 'plan_id');
    }

    public function getLimit(string $key, int $default = 0): int
    {
        return $this->limits[$key] ?? $default;
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }
}

