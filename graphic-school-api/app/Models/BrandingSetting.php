<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BrandingSetting extends Model
{
    use HasFactory;

    protected $table = 'branding_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Get branding value by key
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("branding_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Get all branding settings
     */
    public static function getAll(): array
    {
        return Cache::remember('branding_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get branding settings for frontend
     */
    public static function getForFrontend(): array
    {
        return Cache::remember('branding_frontend', 3600, function () {
            $settings = static::getAll();
            
            // Convert image paths to URLs
            if (!empty($settings['branding.logo.default'])) {
                $settings['branding.logo.default'] = asset('storage/' . $settings['branding.logo.default']);
            }
            if (!empty($settings['branding.logo.dark'])) {
                $settings['branding.logo.dark'] = asset('storage/' . $settings['branding.logo.dark']);
            }
            if (!empty($settings['branding.logo.favicon'])) {
                $settings['branding.logo.favicon'] = asset('storage/' . $settings['branding.logo.favicon']);
            }
            
            // Convert custom font file path to URL if exists
            if (!empty($settings['branding.fonts.custom_file'])) {
                $customFontPath = $settings['branding.fonts.custom_file'];
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($customFontPath)) {
                    $settings['branding.fonts.custom_file'] = asset('storage/' . $customFontPath);
                }
            }
            
            return $settings;
        });
    }

    /**
     * Update or create branding setting
     */
    public static function updateOrCreateSetting(string $key, $value, string $type = 'string'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
            ]
        );

        // Clear cache
        Cache::forget("branding_{$key}");
        Cache::forget('branding_all');
        Cache::forget('branding_frontend');

        return $setting;
    }

    /**
     * Clear all branding cache
     */
    public static function clearCache(): void
    {
        Cache::forget('branding_all');
        Cache::forget('branding_frontend');
        
        // Clear individual keys
        static::pluck('key')->each(function ($key) {
            Cache::forget("branding_{$key}");
        });
    }
}

