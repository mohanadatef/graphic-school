<?php

namespace Modules\LMS\Categories\Models;

use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    protected $fillable = [
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    /**
     * Get name in specific locale
     */
    public function getNameAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->name ?? $this->translations()->first()?->name;
    }

    /**
     * Get name for current locale
     */
    public function getLocalizedNameAttribute(): ?string
    {
        return $this->getNameAttribute(app()->getLocale());
    }
}

