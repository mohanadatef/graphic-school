<?php

namespace Modules\CMS\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'is_public',
    ];

    protected $casts = [
        'value' => 'array',
        'is_public' => 'bool',
    ];

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Get multiple settings by keys
     */
    public static function getManyByKeys(array $keys): array
    {
        return static::whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Update or create setting
     */
    public static function updateOrCreateSetting(
        string $key,
        $value,
        string $type = 'string',
        string $group = 'general',
        bool $isPublic = false
    ): self {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'is_public' => $isPublic,
            ]
        );

        // Clear cache
        Cache::forget("system_setting_{$key}");

        return $setting;
    }

    /**
     * Clear cache for a setting
     */
    public static function clearCache(string $key): void
    {
        Cache::forget("system_setting_{$key}");
    }

    /**
     * Clear all settings cache
     */
    public static function clearAllCache(): void
    {
        Cache::tags(['system_settings'])->flush();
    }
}

