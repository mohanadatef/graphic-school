<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'title',
        'content',
        'config',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'config' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the page this block belongs to
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
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
     * Scope to get enabled blocks
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}

