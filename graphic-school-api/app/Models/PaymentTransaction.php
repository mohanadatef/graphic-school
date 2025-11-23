<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'payment_method_id',
        'amount',
        'status',
        'reference_code',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($transaction) {
            if ($transaction->status === 'success' && $transaction->wasChanged('status')) {
                $transaction->invoice->updateStatus();
            }
        });
    }
}

