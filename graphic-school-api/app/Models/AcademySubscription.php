<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'plan_id',
        'status',
        'started_at',
        'expires_at',
        'trial_ends_at',
        'auto_renew',
        'next_billing_date',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'auto_renew' => 'boolean',
        'next_billing_date' => 'date',
    ];

    public function academy()
    {
        return $this->belongsTo(\Modules\ACL\Users\Models\User::class, 'academy_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function usageTrackers()
    {
        return $this->hasMany(SubscriptionUsageTracker::class, 'academy_id', 'academy_id');
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class, 'academy_id', 'academy_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at > now();
    }

    public function isExpired(): bool
    {
        return $this->expires_at <= now() || $this->status === 'expired';
    }

    public function daysUntilExpiry(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function daysUntilTrialEnds(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }
        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }
}

