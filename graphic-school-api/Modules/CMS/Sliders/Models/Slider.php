<?php

namespace Modules\CMS\Sliders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'sort_order' => 'integer',
    ];

    /**
     * Translation relationships
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\SliderTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(\App\Models\SliderTranslation::class)
            ->where('locale', $locale);
    }

    /**
     * Get translated title
     */
    public function getTranslatedTitleAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->title ?? $this->title ?? $this->translations()->first()?->title;
    }

    /**
     * Get translated subtitle
     */
    public function getTranslatedSubtitleAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->subtitle ?? $this->subtitle ?? $this->translations()->first()?->subtitle;
    }

    /**
     * Get translated button text
     */
    public function getTranslatedButtonTextAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->button_text ?? $this->button_text ?? $this->translations()->first()?->button_text;
    }
}
