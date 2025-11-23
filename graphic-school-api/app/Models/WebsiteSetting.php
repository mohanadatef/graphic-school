<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;

    protected $table = 'website_settings';

    protected $fillable = [
        'academy_id',
        'is_activated',
        'branding',
        'default_language',
        'default_currency',
        'timezone',
        'homepage_id',
        'enabled_pages',
        'general_info',
        'email_settings',
        'payment_settings',
        'activated_at',
    ];

    protected $casts = [
        'is_activated' => 'boolean',
        'branding' => 'array',
        'enabled_pages' => 'array',
        'general_info' => 'array',
        'email_settings' => 'array',
        'payment_settings' => 'array',
        'activated_at' => 'datetime',
    ];

    /**
     * Get the default website settings (singleton)
     */
    public static function getDefault(): self
    {
        try {
            return static::firstOrCreate(
                ['academy_id' => null], // Default academy
                [
                    'is_activated' => false,
                    'default_language' => 'en',
                    'default_currency' => 'USD',
                    'timezone' => 'UTC',
                    'branding' => [
                        'logo' => null,
                        'primary_color' => '#3b82f6',
                        'secondary_color' => '#6366f1',
                        'font_main' => 'Cairo',
                        'font_headings' => 'Poppins',
                        'default_theme' => 'light',
                    ],
                    'enabled_pages' => [
                        'home' => true,
                        'about' => true,
                        'contact' => true,
                        'programs' => true,
                        'community' => true,
                        'faq' => false,
                    ],
                    'general_info' => [
                        'academy_name' => 'Graphic School',
                        'country' => null,
                    ],
                ]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // If table doesn't exist, return a new instance with defaults
            // This allows the system to work even if migrations haven't run
            $instance = new static();
            $instance->academy_id = null;
            $instance->is_activated = false;
            $instance->default_language = 'en';
            $instance->default_currency = 'USD';
            $instance->timezone = 'UTC';
            $instance->branding = [
                'logo' => null,
                'primary_color' => '#3b82f6',
                'secondary_color' => '#6366f1',
                'font_main' => 'Cairo',
                'font_headings' => 'Poppins',
                'default_theme' => 'light',
            ];
            $instance->enabled_pages = [
                'home' => true,
                'about' => true,
                'contact' => true,
                'programs' => true,
                'community' => true,
                'faq' => false,
            ];
            $instance->general_info = [
                'academy_name' => 'Graphic School',
                'country' => null,
            ];
            return $instance;
        }
    }

    /**
     * Check if website is activated
     */
    public function isActivated(): bool
    {
        return $this->is_activated;
    }

    /**
     * Activate website
     */
    public function activate(): void
    {
        $this->update([
            'is_activated' => true,
            'activated_at' => now(),
        ]);
    }

    /**
     * Get public settings (for frontend)
     */
    public function getPublicSettings(): array
    {
        return [
            'is_activated' => $this->is_activated,
            'branding' => $this->branding ?? [],
            'default_language' => $this->default_language,
            'default_currency' => $this->default_currency,
            'timezone' => $this->timezone,
            'enabled_pages' => $this->enabled_pages ?? [],
            'general_info' => $this->general_info ?? [],
        ];
    }
}

