<?php

namespace App\Models;

use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTranslation extends Model
{
    use HasFactory;

    protected $table = 'course_translations';

    protected $fillable = [
        'course_id',
        'locale',
        'title',
        'description',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'meta_title' => 'string',
        'meta_description' => 'string',
    ];

    /**
     * Get the course that owns this translation
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

