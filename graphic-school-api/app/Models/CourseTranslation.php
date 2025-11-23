<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Courses\Models\Course;

class CourseTranslation extends Model
{
    protected $fillable = [
        'course_id',
        'locale',
        'title',
        'description',
        'meta_title',
        'meta_description',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

