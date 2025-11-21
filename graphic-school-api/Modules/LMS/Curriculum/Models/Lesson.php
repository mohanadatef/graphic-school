<?php

namespace Modules\LMS\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'description',
        'content',
        'video_url',
        'video_duration',
        'video_provider',
        'order',
        'lesson_type',
        'is_preview',
        'is_published',
    ];

    protected $casts = [
        'is_preview' => 'bool',
        'is_published' => 'bool',
        'order' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class)->orderBy('order');
    }
}

