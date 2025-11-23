<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\PaymentMethodFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PaymentMethodFactory::new();
    }

    protected $fillable = [
        'name',
        'type',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}

