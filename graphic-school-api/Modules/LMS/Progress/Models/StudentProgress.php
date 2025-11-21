<?php

namespace Modules\LMS\Progress\Models;

use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Curriculum\Models\CourseModule;
use Modules\LMS\Curriculum\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgress extends Model
{
    protected $fillable = [
        'student_id',
        'enrollment_id',
        'course_id',
        'module_id',
        'lesson_id',
        'type',
        'is_completed',
        'progress_percentage',
        'time_spent',
        'started_at',
        'completed_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'is_completed' => 'bool',
        'progress_percentage' => 'integer',
        'time_spent' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}

