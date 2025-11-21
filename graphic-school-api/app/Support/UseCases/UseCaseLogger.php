<?php

namespace App\Support\UseCases;

use Illuminate\Support\Facades\Log;

class UseCaseLogger
{
    /**
     * Log use case start
     */
    public static function start(string $useCaseClass, array $input = []): void
    {
        Log::channel('usecase')->info("UseCase Started: {$useCaseClass}", [
            'usecase' => $useCaseClass,
            'input' => $input,
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log use case success
     */
    public static function success(string $useCaseClass, mixed $result = null): void
    {
        Log::channel('usecase')->info("UseCase Success: {$useCaseClass}", [
            'usecase' => $useCaseClass,
            'result_type' => gettype($result),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log use case failure
     */
    public static function failure(string $useCaseClass, \Throwable $exception): void
    {
        Log::channel('usecase')->error("UseCase Failed: {$useCaseClass}", [
            'usecase' => $useCaseClass,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

