<?php

namespace App\Support\Audit;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Log an audit event
     */
    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        array $changes = [],
        ?string $description = null
    ): void {
        $data = [
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'changes' => $changes,
            'description' => $description,
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel('audit')->info("Audit: {$action} on {$entityType}", $data);
    }

    /**
     * Log creation
     */
    public static function created(string $entityType, int $entityId, array $data = []): void
    {
        self::log('created', $entityType, $entityId, ['new' => $data]);
    }

    /**
     * Log update
     */
    public static function updated(string $entityType, int $entityId, array $oldData, array $newData): void
    {
        $changes = [];
        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        self::log('updated', $entityType, $entityId, $changes);
    }

    /**
     * Log deletion
     */
    public static function deleted(string $entityType, int $entityId, array $data = []): void
    {
        self::log('deleted', $entityType, $entityId, ['deleted' => $data]);
    }

    /**
     * Log custom action
     */
    public static function action(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?string $description = null
    ): void {
        self::log($action, $entityType, $entityId, [], $description);
    }
}

