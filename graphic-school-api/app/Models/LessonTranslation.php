<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Curriculum\Models\Lesson;

class LessonTranslation extends Model
{
    protected $fillable = [
        'lesson_id',
        'locale',
        'title',
        'description',
        'content',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}

