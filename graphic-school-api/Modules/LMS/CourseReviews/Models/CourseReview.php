<?php

namespace Modules\LMS\CourseReviews\Models;

use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'instructor_id',
        'rating_course',
        'rating_instructor',
        'comment',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'bool',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}

