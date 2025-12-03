<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'meta_description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'meta_description' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get page blocks (sections)
     */
    public function blocks()
    {
        return $this->hasMany(PageBlock::class)->orderBy('sort_order');
    }

    /**
     * Get enabled blocks only
     */
    public function enabledBlocks()
    {
        return $this->blocks()->where('is_enabled', true);
    }

    /**
     * Get title for specific language
     */
    public function getTitle(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $titles = $this->title ?? [];
        return $titles[$locale] ?? $titles['en'] ?? $titles[array_key_first($titles)] ?? null;
    }

    /**
     * Get content for specific language
     */
    public function getContent(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $contents = $this->content ?? [];
        return $contents[$locale] ?? $contents['en'] ?? $contents[array_key_first($contents)] ?? null;
    }

    /**
     * Get meta description for specific language
     */
    public function getMetaDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $descriptions = $this->meta_description ?? [];
        return $descriptions[$locale] ?? $descriptions['en'] ?? $descriptions[array_key_first($descriptions)] ?? null;
    }

    /**
     * Scope to get active pages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
