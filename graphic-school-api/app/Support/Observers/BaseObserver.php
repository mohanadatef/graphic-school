<?php

namespace App\Support\Observers;

use App\Support\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;

abstract class BaseObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        AuditLogger::created(
            $model->getTable(),
            $model->getKey(),
            $model->getAttributes()
        );
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        AuditLogger::updated(
            $model->getTable(),
            $model->getKey(),
            $model->getOriginal(),
            $model->getChanges()
        );
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        AuditLogger::deleted(
            $model->getTable(),
            $model->getKey(),
            $model->getAttributes()
        );
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        AuditLogger::action(
            'restored',
            $model->getTable(),
            $model->getKey(),
            'Model restored'
        );
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        AuditLogger::action(
            'force_deleted',
            $model->getTable(),
            $model->getKey(),
            'Model force deleted'
        );
    }
}

