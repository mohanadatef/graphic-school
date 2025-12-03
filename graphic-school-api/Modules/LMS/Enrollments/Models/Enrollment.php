<?php

namespace Modules\LMS\Enrollments\Models;

use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'group_id',
        'status',
        'can_attend',
        'approved_by',
        'approved_at',
        'note',
    ];

    protected $casts = [
        'can_attend' => 'bool',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

}

