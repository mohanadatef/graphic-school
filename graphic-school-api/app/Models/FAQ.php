<?php

namespace App\Models;

use Database\Factories\FAQFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CHANGE-002: CMS FAQ Management
 */
class FAQ extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return FAQFactory::new();
    }

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'answer',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'bool',
    ];

    /**
     * Scope: Active FAQs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Translation relationships
     */
    public function translations()
    {
        return $this->hasMany(FAQTranslation::class, 'faq_id');
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(FAQTranslation::class, 'faq_id')
            ->where('locale', $locale);
    }

    /**
     * Get translated question
     */
    public function getTranslatedQuestionAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->question ?? $this->question ?? $this->translations()->first()?->question;
    }

    /**
     * Get translated answer
     */
    public function getTranslatedAnswerAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->answer ?? $this->answer ?? $this->translations()->first()?->answer;
    }
}
