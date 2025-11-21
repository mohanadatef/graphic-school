<?php

namespace App\Models;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CHANGE-002: CMS Page Builder
 */
class Page extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PageFactory::new();
    }

    protected $fillable = [
        'slug',
        'title',
        'content',
        'template',
        'sections',
        'meta_title',
        'meta_description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'sections' => 'array',
        'seo' => 'array',
        'settings' => 'array',
        'is_active' => 'bool',
        'sort_order' => 'integer',
    ];

    /**
     * Get page by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * Scope: Active pages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
