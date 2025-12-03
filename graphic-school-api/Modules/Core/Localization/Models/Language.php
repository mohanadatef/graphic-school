<?php

namespace Modules\Core\Localization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'image_path',
        'is_active',
        'is_default',
        'is_rtl',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_rtl' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the full URL for the language image
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        // If it's already a full URL, return it
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        // Otherwise, return the storage URL
        return asset('storage/' . $this->image_path);
    }

    /**
     * Scope to get only active languages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to get default language
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get RTL languages
     */
    public function scopeRtl($query)
    {
        return $query->where('is_rtl', true);
    }

    /**
     * Get default language
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first() 
            ?? static::where('code', 'en')->first();
    }
}

