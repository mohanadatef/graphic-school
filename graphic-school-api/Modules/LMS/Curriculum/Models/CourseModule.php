<?php

namespace Modules\LMS\Curriculum\Models;

use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_published',
        'is_preview',
    ];

    protected $casts = [
        'is_published' => 'bool',
        'is_preview' => 'bool',
        'order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->lessons()->where('is_published', true);
    }

    /**
     * Translation relationships
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\CourseModuleTranslation::class, 'module_id');
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(\App\Models\CourseModuleTranslation::class, 'module_id')
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
}

