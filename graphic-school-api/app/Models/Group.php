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
        'name',
        'capacity', // This is max_students
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

    /**
     * Accessor for max_students (alias of capacity)
     */
    public function getMaxStudentsAttribute()
    {
        return $this->capacity;
    }

    /**
     * Mutator for max_students (alias of capacity)
     */
    public function setMaxStudentsAttribute($value)
    {
        $this->capacity = $value;
    }

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

    /**
     * Boot method - Business logic
     */
    protected static function boot()
    {
        parent::boot();

        // Validate: Student enrollment checks group capacity
        static::saving(function ($group) {
            if ($group->exists && $group->isDirty('capacity')) {
                $currentEnrollments = $group->students()->count();
                if ($currentEnrollments > $group->capacity) {
                    throw new \Exception("Cannot reduce capacity below current enrollments ({$currentEnrollments} students).");
                }
            }
        });

        // Validate: Instructor cannot be assigned to two groups at the same time
        static::saving(function ($group) {
            // Skip validation if batch_id is missing or if group is being created without batch loaded
            if (!$group->instructor_id || !$group->batch_id) {
                return;
            }
            
            // Load batch if not already loaded
            if (!$group->relationLoaded('batch')) {
                $group->load('batch');
            }
            
            if (!$group->batch || !$group->batch->start_date) {
                return; // Skip validation if batch data is missing
            }
            
            $batchStartDate = $group->batch->start_date;
            $batchEndDate = $group->batch->end_date ?? '9999-12-31';
            
            $conflictingGroups = static::where('id', '!=', $group->id ?? 0)
                ->where('instructor_id', $group->instructor_id)
                ->whereHas('batch', function ($query) use ($batchStartDate, $batchEndDate) {
                    $query->where(function ($q) use ($batchStartDate, $batchEndDate) {
                        $q->whereBetween('start_date', [$batchStartDate, $batchEndDate])
                            ->orWhereBetween('end_date', [$batchStartDate, $batchEndDate])
                            ->orWhere(function ($subQ) use ($batchStartDate, $batchEndDate) {
                                $subQ->where('start_date', '<=', $batchStartDate)
                                     ->where('end_date', '>=', $batchEndDate);
                            });
                    });
                })
                ->exists();

            if ($conflictingGroups) {
                throw new \Exception('Instructor cannot be assigned to two groups at the same time.');
            }
        });

        // Group sessions inherit dates from Batch schedule
        static::created(function ($group) {
            if ($group->batch && $group->batch->schedules->isNotEmpty()) {
                // Sessions will be created based on batch schedule
                // This is handled in the SessionController or a service
            }
        });
    }

    /**
     * Check if group has capacity for enrollment
     */
    public function hasCapacity(): bool
    {
        return $this->students()->count() < $this->capacity;
    }

    /**
     * Get available spots
     */
    public function getAvailableSpotsAttribute(): int
    {
        return max(0, $this->capacity - $this->students()->count());
    }
}

