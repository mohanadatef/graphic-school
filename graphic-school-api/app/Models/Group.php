<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Sessions\Models\Session;
use Database\Factories\GroupFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'code',
        'capacity',
        'room',
        'instructor_id',
        'is_active',
        'extras',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'extras' => 'array',
    ];

    protected static function newFactory()
    {
        return GroupFactory::new();
    }

    /**
     * Relationships
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function instructor()
    {
        return $this->belongsTo(\Modules\ACL\Users\Models\User::class, 'instructor_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            \Modules\ACL\Users\Models\User::class,
            'group_student',
            'group_id',
            'student_id'
        )->withTimestamps()->withPivot('enrolled_at');
    }

    public function instructors()
    {
        return $this->belongsToMany(
            \Modules\ACL\Users\Models\User::class,
            'group_instructor',
            'group_id',
            'instructor_id'
        )->withTimestamps()->withPivot('assigned_at');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function translations()
    {
        return $this->hasMany(GroupTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(GroupTranslation::class)
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
     * Get program through batch
     */
    public function program()
    {
        return $this->hasOneThrough(Program::class, Batch::class, 'id', 'id', 'batch_id', 'program_id');
    }
}

