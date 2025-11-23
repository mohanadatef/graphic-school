<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'plan_id',
        'amount',
        'currency',
        'status',
        'billing_period',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function academy()
    {
        return $this->belongsTo(\Modules\ACL\Users\Models\User::class, 'academy_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'invoice_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}

