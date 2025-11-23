<?php

namespace Modules\CMS\Testimonials\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'relation',
        'rating',
        'comment',
        'avatar_path',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'bool',
    ];

    /**
     * Translation relationships
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\TestimonialTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(\App\Models\TestimonialTranslation::class)
            ->where('locale', $locale);
    }

    /**
     * Get translated comment
     */
    public function getTranslatedCommentAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->comment ?? $this->comment ?? $this->translations()->first()?->comment;
    }
}

