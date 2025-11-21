<?php

namespace Modules\LMS\Assessments\Models;

use Modules\ACL\Users\Models\User;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'student_id',
        'quiz_id',
        'enrollment_id',
        'answers',
        'score',
        'total_points',
        'percentage',
        'is_passed',
        'started_at',
        'completed_at',
        'time_taken',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_points' => 'integer',
        'percentage' => 'decimal:2',
        'is_passed' => 'bool',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_taken' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}

