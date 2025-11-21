<?php

namespace App\Support\Health;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;

class HealthChecker
{
    /**
     * Perform comprehensive health check
     */
    public static function check(): array
    {
        return [
            'status' => self::getOverallStatus(),
            'timestamp' => now()->toIso8601String(),
            'checks' => [
                'database' => self::checkDatabase(),
                'cache' => self::checkCache(),
                'storage' => self::checkStorage(),
                'queue' => self::checkQueue(),
                'memory' => self::checkMemory(),
                'disk' => self::checkDisk(),
            ],
        ];
    }

    /**
     * Get overall status
     */
    protected static function getOverallStatus(): string
    {
        $checks = [
            self::checkDatabase(),
            self::checkCache(),
            self::checkStorage(),
            self::checkQueue(),
        ];

        $allHealthy = collect($checks)->every(fn ($check) => $check['status'] === 'healthy');

        return $allHealthy ? 'healthy' : 'degraded';
    }

    /**
     * Check database connection
     */
    protected static function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'healthy',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache
     */
    protected static function checkCache(): array
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);

            if ($value === 'test') {
                return [
                    'status' => 'healthy',
                    'message' => 'Cache is working',
                ];
            }

            return [
                'status' => 'unhealthy',
                'message' => 'Cache read/write failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Cache check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage
     */
    protected static function checkStorage(): array
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::disk('public')->put($testFile, 'test');
            $exists = Storage::disk('public')->exists($testFile);
            Storage::disk('public')->delete($testFile);

            if ($exists) {
                return [
                    'status' => 'healthy',
                    'message' => 'Storage is writable',
                ];
            }

            return [
                'status' => 'unhealthy',
                'message' => 'Storage write failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Storage check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue
     */
    protected static function checkQueue(): array
    {
        try {
            // Check if queue driver is configured
            $driver = config('queue.default');
            
            return [
                'status' => 'healthy',
                'message' => "Queue driver: {$driver}",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Queue check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check memory usage
     */
    protected static function checkMemory(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $memoryPeak = memory_get_peak_usage(true);

        return [
            'status' => 'healthy',
            'current' => self::formatBytes($memoryUsage),
            'peak' => self::formatBytes($memoryPeak),
            'limit' => $memoryLimit,
        ];
    }

    /**
     * Check disk usage
     */
    protected static function checkDisk(): array
    {
        $totalSpace = disk_total_space(storage_path());
        $freeSpace = disk_free_space(storage_path());
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercent = ($usedSpace / $totalSpace) * 100;

        return [
            'status' => $usagePercent > 90 ? 'warning' : 'healthy',
            'total' => self::formatBytes($totalSpace),
            'used' => self::formatBytes($usedSpace),
            'free' => self::formatBytes($freeSpace),
            'usage_percent' => round($usagePercent, 2),
        ];
    }

    /**
     * Format bytes to human readable
     */
    protected static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

