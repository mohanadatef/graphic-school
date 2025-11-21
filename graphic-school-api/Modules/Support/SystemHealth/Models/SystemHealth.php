<?php

namespace Modules\Support\SystemHealth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemHealth extends Model
{
    use HasFactory;

    protected $table = 'system_health';

    protected $fillable = [
        'status', // healthy, degraded, down
        'message',
        'checks',
        'last_check',
    ];

    protected $casts = [
        'checks' => 'array',
        'last_check' => 'datetime',
    ];

    /**
     * Get current health status
     */
    public static function getCurrent(): ?self
    {
        return static::latest('last_check')->first();
    }

    /**
     * Update health status
     */
    public static function updateStatus(string $status, string $message, array $checks = []): self
    {
        return static::updateOrCreate(
            ['id' => 1],
            [
                'status' => $status,
                'message' => $message,
                'checks' => $checks,
                'last_check' => now(),
            ]
        );
    }
}

