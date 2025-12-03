<?php

namespace App\Models;

use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\SessionTemplateFactory;

class SessionTemplate extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return SessionTemplateFactory::new();
    }

    protected $fillable = [
        'course_id',
        'title',
        'session_order',
        'description',
        'duration_minutes',
        'default_start_time',
        'default_end_time',
        'is_required',
        'materials',
    ];

    protected $casts = [
        'session_order' => 'integer',
        'duration_minutes' => 'integer',
        'is_required' => 'boolean',
        'materials' => 'array',
    ];

    /**
     * Relationships
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all group sessions that use this template
     */
    public function groupSessions()
    {
        return $this->hasMany(\Modules\LMS\Sessions\Models\GroupSession::class, 'session_template_id');
    }
}

