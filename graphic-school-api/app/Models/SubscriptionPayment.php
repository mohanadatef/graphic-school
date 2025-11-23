<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'method_id',
        'status',
        'reference_code',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(SubscriptionInvoice::class, 'invoice_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class, 'method_id');
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }
}

