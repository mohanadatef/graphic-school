<?php

namespace Modules\Operations\Logging\Traits;

use Modules\Operations\Logging\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            static::logActivity('created', $model);
        });

        static::updated(function ($model) {
            static::logActivity('updated', $model);
        });

        static::deleted(function ($model) {
            static::logActivity('deleted', $model);
        });
    }

    /**
     * Log an activity
     */
    protected static function logActivity(string $action, $model): void
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'updated') {
            $oldValues = $model->getOriginal();
            $newValues = $model->getChanges();
        } elseif ($action === 'created') {
            $newValues = $model->getAttributes();
        } elseif ($action === 'deleted') {
            $oldValues = $model->getAttributes();
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'description' => static::getActivityDescription($action, $model),
        ]);
    }

    /**
     * Get activity description
     */
    protected static function getActivityDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $identifier = $model->getActivityIdentifier();

        return ucfirst($action) . " {$modelName}: {$identifier}";
    }

    /**
     * Get identifier for activity log
     * Override this method in your model
     */
    public function getActivityIdentifier(): string
    {
        return $this->id ?? 'N/A';
    }
}

