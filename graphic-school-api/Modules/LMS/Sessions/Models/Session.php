<?php

namespace Modules\LMS\Sessions\Models;

use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Attendance\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'session_order',
        'session_date',
        'start_time',
        'end_time',
        'meeting_link',
        'note',
        'status',
        'student_comment',
        'student_file_path',
        'instructor_comment',
        'supervisor_comment',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}

