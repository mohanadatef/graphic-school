<?php

namespace App\Support\Models;

trait VersioningTrait
{
    /**
     * Boot the versioning trait.
     */
    public static function bootVersioningTrait(): void
    {
        static::updating(function ($model) {
            if ($model->isDirty()) {
                // Create version record
                $version = [
                    'model_type' => get_class($model),
                    'model_id' => $model->getKey(),
                    'data' => $model->getOriginal(),
                    'changes' => $model->getChanges(),
                    'version' => ($model->versions()->max('version') ?? 0) + 1,
                    'created_at' => now(),
                ];

                $model->versions()->create($version);
            }
        });
    }

    /**
     * Get all versions for this model.
     */
    public function versions()
    {
        return $this->morphMany(\App\Models\Version::class, 'versionable');
    }

    /**
     * Get the latest version.
     */
    public function latestVersion()
    {
        return $this->versions()->latest()->first();
    }
}

