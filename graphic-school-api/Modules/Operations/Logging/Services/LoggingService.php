<?php

namespace Modules\Operations\Logging\Services;

use Modules\Operations\Logging\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LoggingService
{
    /**
     * Get activity logs with filters
     */
    public function getLogs(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $query = ActivityLog::with('user')
            ->orderByDesc('created_at');

        if (!empty($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->forAction($filters['action']);
        }

        if (!empty($filters['model_type'])) {
            $query->forModel($filters['model_type'], $filters['model_id'] ?? null);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->inDateRange(
                Carbon::parse($filters['start_date']),
                Carbon::parse($filters['end_date'])
            );
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get logs for a specific model
     */
    public function getModelLogs(string $modelType, int $modelId): Collection
    {
        return ActivityLog::forModel($modelType, $modelId)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get logs for a specific user
     */
    public function getUserLogs(int $userId, int $perPage = 50): LengthAwarePaginator
    {
        return ActivityLog::forUser($userId)
            ->with('model')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Export logs to CSV
     */
    public function exportToCsv(array $filters): string
    {
        $logs = $this->getLogs($filters, 10000)->items();

        $csv = "ID,User,Action,Model,Model ID,Description,IP Address,Date\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%d,%s,%s,%s\n",
                $log->id,
                $log->user?->name ?? 'System',
                $log->action,
                $log->model_type,
                $log->model_id ?? 0,
                $log->description ?? '',
                $log->ip_address ?? '',
                $log->created_at->format('Y-m-d H:i:s')
            );
        }

        return $csv;
    }
}

