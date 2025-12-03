<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Sessions\Models\GroupSession;
use Database\Factories\GroupFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'code',
        'name',
        'capacity', // This is max_students
        'start_date',
        'end_date',
        'notes',
        'room',
        'instructor_id',
        'is_active',
        'extras',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
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
    public function course()
    {
        return $this->belongsTo(\Modules\LMS\Courses\Models\Course::class);
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

    /**
     * Group sessions for this group
     */
    public function groupSessions()
    {
        return $this->hasMany(GroupSession::class);
    }

    /**
     * Alias for backward compatibility
     */
    public function sessions()
    {
        return $this->groupSessions();
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

        // Validate: Instructor cannot be assigned to two groups at the same time (for same course)
        static::saving(function ($group) {
            if (!$group->instructor_id || !$group->course_id) {
                return;
            }
            
            // Load course if not already loaded
            if (!$group->relationLoaded('course')) {
                $group->load('course');
            }
            
            if (!$group->course) {
                return;
            }
            
            // Check for conflicting groups in the same course
            $conflictingGroups = static::where('id', '!=', $group->id ?? 0)
                ->where('instructor_id', $group->instructor_id)
                ->where('course_id', $group->course_id)
                ->exists();

            if ($conflictingGroups) {
                throw new \Exception('Instructor cannot be assigned to two groups in the same course.');
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

