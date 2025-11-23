<?php

namespace Modules\LMS\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'description',
        'content',
        'video_url',
        'video_duration',
        'video_provider',
        'order',
        'lesson_type',
        'is_preview',
        'is_published',
    ];

    protected $casts = [
        'is_preview' => 'bool',
        'is_published' => 'bool',
        'order' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class)->orderBy('order');
    }

    /**
     * Translation relationships
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\LessonTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(\App\Models\LessonTranslation::class)
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
     * Get translated description
     */
    public function getTranslatedDescriptionAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->description ?? $this->description ?? $this->translations()->first()?->description;
    }

    /**
     * Get translated content
     */
    public function getTranslatedContentAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->content ?? $this->content ?? $this->translations()->first()?->content;
    }
}

