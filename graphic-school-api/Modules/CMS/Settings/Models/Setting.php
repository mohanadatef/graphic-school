<?php

namespace Modules\CMS\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public static function getValue(string $key, $default = null)
    {
        return cache()->remember("setting_{$key}", 3600, fn () => optional(static::where('key', $key)->first())->value ?? $default);
    }

    public static function getManyByKeys(array $keys): array
    {
        return static::whereIn('key', $keys)->pluck('value', 'key')->toArray();
    }

    public static function updateOrCreateSetting(string $key, $value, string $type = 'string', string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
            ]
        );
    }
}

