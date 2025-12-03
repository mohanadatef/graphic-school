<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get default currency
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get default currency
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first() 
            ?? static::where('code', 'EGP')->first();
    }

    /**
     * Format amount with currency symbol
     */
    public function formatAmount(float $amount, bool $showCode = false): string
    {
        $formatted = number_format($amount, 2);
        $symbol = $this->symbol;
        
        if ($showCode) {
            return "{$formatted} {$symbol} ({$this->code})";
        }
        
        return "{$symbol}{$formatted}";
    }
}

