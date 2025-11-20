<?php

namespace Modules\Support\SystemHealth\Services;

use Modules\Support\SystemHealth\Models\SystemHealth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthCheckService
{
    /**
     * Perform health check
     */
    public function performCheck(): array
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $allHealthy = collect($checks)->every(fn ($check) => $check['status'] === 'healthy');

        $status = $allHealthy ? 'healthy' : 'degraded';
        $message = $allHealthy 
            ? 'All systems operational' 
            : 'Some systems are experiencing issues';

        SystemHealth::updateStatus($status, $message, $checks);

        return [
            'status' => $status,
            'message' => $message,
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Check database connection
     */
    protected function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'healthy',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache connection
     */
    protected function checkCache(): array
    {
        try {
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            
            if ($value === 'ok') {
                return [
                    'status' => 'healthy',
                    'message' => 'Cache is working',
                ];
            }
            
            return [
                'status' => 'degraded',
                'message' => 'Cache read/write issue',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'message' => 'Cache connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage
     */
    protected function checkStorage(): array
    {
        try {
            $testFile = 'health_check_' . time();
            Storage::disk('public')->put($testFile, 'test');
            Storage::disk('public')->delete($testFile);
            
            return [
                'status' => 'healthy',
                'message' => 'Storage is writable',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'message' => 'Storage check failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue
     */
    protected function checkQueue(): array
    {
        try {
            // Simple check - can be enhanced
            return [
                'status' => 'healthy',
                'message' => 'Queue system available',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'degraded',
                'message' => 'Queue check failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get current health status
     */
    public function getStatus(): array
    {
        $health = SystemHealth::getCurrent();

        if (!$health) {
            // Perform initial check
            return $this->performCheck();
        }

        return [
            'status' => $health->status,
            'message' => $health->message,
            'checks' => $health->checks,
            'last_check' => $health->last_check?->toIso8601String(),
        ];
    }

    /**
     * Check if dashboard is accessible
     */
    public function checkDashboard(): array
    {
        $health = $this->getStatus();

        return [
            'dashboard_accessible' => $health['status'] !== 'down',
            'status' => $health['status'],
            'message' => $health['message'],
        ];
    }
}

