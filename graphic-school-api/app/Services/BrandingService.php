<?php

namespace App\Services;

use App\Models\BrandingSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;

class BrandingService
{
    /**
     * Get all branding settings
     */
    public function all(): array
    {
        return BrandingSetting::getAll();
    }

    /**
     * Get branding setting by key
     */
    public function get(string $key, $default = null)
    {
        return BrandingSetting::getValue($key, $default);
    }

    /**
     * Set branding setting
     */
    public function set(string $key, $value, string $type = 'string'): void
    {
        BrandingSetting::updateOrCreateSetting($key, $value, $type);
    }

    /**
     * Load branding for frontend
     */
    public function loadForFrontend(): array
    {
        $settings = BrandingSetting::getForFrontend();
        
        // Structure font data
        $fontSource = $settings['branding.fonts.source'] ?? 'system';
        $fontMain = $settings['branding.fonts.main'] ?? 'Inter';
        $fontHeadings = $settings['branding.fonts.headings'] ?? 'Inter';
        $customFontFile = $settings['branding.fonts.custom_file'] ?? null;
        
        // Get available fonts
        $availableFontsJson = $settings['branding.fonts.available_fonts'] ?? '[]';
        $availableFonts = json_decode($availableFontsJson, true) ?? [];
        
        // Convert custom font file path to URL if exists
        $customFontUrl = null;
        if ($customFontFile && Storage::disk('public')->exists($customFontFile)) {
            $customFontUrl = asset('storage/' . $customFontFile);
        }
        
        // Add structured font data
        $settings['fonts'] = [
            'source' => $fontSource,
            'main' => $fontMain,
            'headings' => $fontHeadings,
            'custom_file_url' => $customFontUrl,
            'available_fonts' => $availableFonts,
        ];
        
        return $settings;
    }

    /**
     * Update branding settings
     */
    public function update(array $data, ?UploadedFile $logoDefault = null, ?UploadedFile $logoDark = null, ?UploadedFile $favicon = null, ?UploadedFile $customFont = null): void
    {
        // Handle logo uploads
        if ($logoDefault) {
            $oldLogo = BrandingSetting::getValue('branding.logo.default');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $logoDefault->store('branding', 'public');
            $this->set('branding.logo.default', $path, 'image');
        }

        if ($logoDark) {
            $oldLogo = BrandingSetting::getValue('branding.logo.dark');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $logoDark->store('branding', 'public');
            $this->set('branding.logo.dark', $path, 'image');
        }

        if ($favicon) {
            $oldFavicon = BrandingSetting::getValue('branding.logo.favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $path = $favicon->store('branding', 'public');
            $this->set('branding.logo.favicon', $path, 'image');
        }

        // Handle custom font upload
        if ($customFont) {
            $oldFont = BrandingSetting::getValue('branding.fonts.custom_file');
            if ($oldFont && Storage::disk('public')->exists($oldFont)) {
                Storage::disk('public')->delete($oldFont);
            }
            $path = $customFont->store('branding/fonts', 'public');
            $this->set('branding.fonts.custom_file', $path, 'string');
            $this->set('branding.fonts.source', 'custom', 'string');
            $this->set('branding.fonts.main', 'CustomFont', 'font');
            $this->set('branding.fonts.headings', 'CustomFont', 'font');
        } elseif (isset($data['branding.fonts.source']) && $data['branding.fonts.source'] === 'system') {
            // When switching to system, don't delete custom font file, just update source
            $this->set('branding.fonts.source', 'system', 'string');
        }

        // Update other settings
        foreach ($data as $key => $value) {
            if (in_array($key, ['logo_default', 'logo_dark', 'favicon', 'custom_font'])) {
                continue; // Already handled above
            }

            $type = $this->determineType($key);
            $this->set($key, $value, $type);
        }

        // Dispatch event
        Event::dispatch(new \App\Events\BrandingUpdated());

        // Clear cache
        BrandingSetting::clearCache();
    }

    /**
     * Determine setting type from key
     */
    protected function determineType(string $key): string
    {
        if (str_starts_with($key, 'branding.colors.')) {
            return 'color';
        }
        if (str_starts_with($key, 'branding.fonts.')) {
            return 'font';
        }
        if (str_starts_with($key, 'branding.logo.')) {
            return 'image';
        }
        if (str_starts_with($key, 'branding.layout.')) {
            return 'string';
        }
        return 'string';
    }
}

