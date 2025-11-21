<?php

namespace App\Support\Jobs;

use Illuminate\Support\Facades\Log;

class JobLogger
{
    /**
     * Log job dispatch
     */
    public static function dispatch(string $jobClass, array $payload = []): void
    {
        Log::channel('job')->info("Job Dispatched: {$jobClass}", [
            'job' => $jobClass,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log job start
     */
    public static function start(string $jobClass, string $jobId): void
    {
        Log::channel('job')->info("Job Started: {$jobClass}", [
            'job' => $jobClass,
            'job_id' => $jobId,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log job success
     */
    public static function success(string $jobClass, string $jobId, array $result = []): void
    {
        Log::channel('job')->info("Job Completed: {$jobClass}", [
            'job' => $jobClass,
            'job_id' => $jobId,
            'result' => $result,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log job failure
     */
    public static function failure(string $jobClass, string $jobId, \Throwable $exception): void
    {
        Log::channel('job')->error("Job Failed: {$jobClass}", [
            'job' => $jobClass,
            'job_id' => $jobId,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

