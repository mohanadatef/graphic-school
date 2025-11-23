<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACL\Users\Models\User;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'submitted_at',
        'file_url',
        'text_submission',
        'grade',
        'feedback',
        'graded_at',
        'graded_by',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'grade' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isLate(): bool
    {
        return $this->submitted_at > $this->assignment->due_date;
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded' && $this->grade !== null;
    }
}

