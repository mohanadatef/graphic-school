<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACL\Users\Models\User;

class GradebookEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'program_id',
        'batch_id',
        'assignment_grade',
        'attendance_percentage',
        'participation_grade',
        'overall_grade',
        'notes',
    ];

    protected $casts = [
        'assignment_grade' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'participation_grade' => 'decimal:2',
        'overall_grade' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function calculateOverallGrade(): void
    {
        // Weighted calculation: 40% assignments, 30% attendance, 30% participation
        $this->overall_grade = (
            ($this->assignment_grade * 0.4) +
            ($this->attendance_percentage * 0.3) +
            ($this->participation_grade * 0.3)
        );
        $this->save();
    }
}

