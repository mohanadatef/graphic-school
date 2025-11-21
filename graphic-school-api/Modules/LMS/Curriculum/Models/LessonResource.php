<?php

namespace Modules\LMS\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonResource extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'external_url',
        'is_downloadable',
        'download_count',
        'order',
    ];

    protected $casts = [
        'is_downloadable' => 'bool',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'order' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}

