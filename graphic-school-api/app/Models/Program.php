<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Database\Factories\ProgramFactory;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'type',
        'duration_weeks',
        'price',
        'level',
        'image_path',
        'is_active',
        'sort_order',
        'extras',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_weeks' => 'integer',
        'price' => 'decimal:2',
        'sort_order' => 'integer',
        'extras' => 'array',
    ];

    protected static function newFactory()
    {
        return ProgramFactory::new();
    }

    /**
     * Relationships
     */
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function translations()
    {
        return $this->hasMany(ProgramTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(ProgramTranslation::class)
            ->where('locale', $locale);
    }

    /**
     * Get translated attribute
     */
    public function getTranslated(string $key, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);
        
        if ($translation && $translation->$key) {
            return $translation->$key;
        }
        
        // Fallback to default locale (en)
        $fallbackTranslation = $this->translations->firstWhere('locale', config('app.fallback_locale', 'en'));
        return $fallbackTranslation->$key ?? null;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($program) {
            if (empty($program->slug)) {
                $program->slug = Str::slug($program->getTranslated('title', 'en') ?? 'program-' . time());
            }
        });

        // Cascade soft-delete batches when program is deleted
        static::deleted(function ($program) {
            if ($program->isForceDeleting()) {
                // Hard delete - delete batches permanently
                $program->batches()->withTrashed()->forceDelete();
            } else {
                // Soft delete - soft delete batches
                $program->batches()->delete();
            }
        });
    }
}

