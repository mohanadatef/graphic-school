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
        'group_id',
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

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Translation relationships
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\SessionTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(\App\Models\SessionTranslation::class)
            ->where('locale', $locale);
    }

    /**
     * Get translated title
     */
    public function getTranslatedTitleAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->title ?? $this->title ?? $this->translations()->first()?->title;
    }

    /**
     * Get translated note
     */
    public function getTranslatedNoteAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation?->note ?? $this->note ?? $this->translations()->first()?->note;
    }
}

