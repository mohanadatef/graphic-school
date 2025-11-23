<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\BatchFactory;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'code',
        'start_date',
        'end_date',
        'max_students',
        'is_active',
        'extras',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'max_students' => 'integer',
        'extras' => 'array',
    ];

    protected static function newFactory()
    {
        return BatchFactory::new();
    }

    /**
     * Relationships
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function schedules()
    {
        return $this->hasMany(BatchSchedule::class);
    }

    public function translations()
    {
        return $this->hasMany(BatchTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(BatchTranslation::class)
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
}

