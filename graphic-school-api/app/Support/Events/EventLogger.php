<?php

namespace App\Support\Events;

use Illuminate\Support\Facades\Log;

class EventLogger
{
    /**
     * Log event dispatch
     */
    public static function dispatch(string $eventClass, array $payload = []): void
    {
        Log::channel('usecase')->info("Event Dispatched: {$eventClass}", [
            'event' => $eventClass,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log event handled
     */
    public static function handled(string $eventClass, string $listenerClass, array $payload = []): void
    {
        Log::channel('usecase')->info("Event Handled: {$eventClass} by {$listenerClass}", [
            'event' => $eventClass,
            'listener' => $listenerClass,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log event failed
     */
    public static function failed(string $eventClass, string $listenerClass, \Throwable $exception): void
    {
        Log::channel('usecase')->error("Event Failed: {$eventClass} in {$listenerClass}", [
            'event' => $eventClass,
            'listener' => $listenerClass,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

