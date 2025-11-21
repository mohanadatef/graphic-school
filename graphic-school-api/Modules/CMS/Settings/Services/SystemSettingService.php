<?php

namespace Modules\CMS\Settings\Services;

use Modules\CMS\Settings\Models\SystemSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SystemSettingService
{
    /**
     * Get settings by keys
     */
    public function getSettings(array $keys): array
    {
        $settings = SystemSetting::getManyByKeys($keys);

        // Process special settings
        if (isset($settings['logo'])) {
            $settings['logo_url'] = Storage::disk('public')->url($settings['logo']);
        }

        return $settings;
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): array
    {
        return SystemSetting::getByGroup($group);
    }

    /**
     * Get all public settings (for frontend)
     */
    public function getPublicSettings(): array
    {
        return Cache::remember('public_settings', 3600, function () {
            return SystemSetting::where('is_public', true)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Update settings
     */
    public function update(array $data, ?UploadedFile $logo = null): void
    {
        if ($logo) {
            $oldLogo = SystemSetting::getValue('logo');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $data['logo'] = $logo->store('settings', 'public');
        }

        unset($data['logo_image']);

        foreach ($data as $key => $value) {
            $group = $this->determineGroup($key);
            $type = $this->determineType($value);

            SystemSetting::updateOrCreateSetting(
                $key,
                $value,
                $type,
                $group,
                $this->isPublicSetting($key)
            );
        }

        // Clear public settings cache
        Cache::forget('public_settings');
    }

    /**
     * Update colors
     */
    public function updateColors(array $colors): void
    {
        SystemSetting::updateOrCreateSetting(
            'colors',
            $colors,
            'json',
            'colors',
            true // Public for frontend
        );
    }

    /**
     * Update section visibility
     */
    public function updateSectionVisibility(string $section, bool $visible): void
    {
        $sections = SystemSetting::getValue('sections', []);
        $sections[$section] = $visible;

        SystemSetting::updateOrCreateSetting(
            'sections',
            $sections,
            'json',
            'sections',
            true
        );
    }

    /**
     * Determine group for a setting key
     */
    protected function determineGroup(string $key): string
    {
        if (str_starts_with($key, 'color_') || $key === 'colors') {
            return 'colors';
        }

        if (in_array($key, ['slider_enabled', 'testimonials_enabled'])) {
            return 'sections';
        }

        if (in_array($key, ['default_language', 'available_languages'])) {
            return 'languages';
        }

        return 'general';
    }

    /**
     * Determine type for a value
     */
    protected function determineType($value): string
    {
        if (is_array($value)) {
            return 'array';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return 'integer';
        }

        return 'string';
    }

    /**
     * Check if setting is public
     */
    protected function isPublicSetting(string $key): bool
    {
        $publicKeys = [
            'site_name',
            'logo',
            'colors',
            'sections',
            'default_language',
        ];

        return in_array($key, $publicKeys);
    }
}

