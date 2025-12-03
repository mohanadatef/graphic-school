<?php

namespace Modules\LMS\Sessions\Models;

use App\Models\Group;
use App\Models\SessionTemplate;
use Modules\LMS\Attendance\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\GroupSessionFactory;

class GroupSession extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return GroupSessionFactory::new();
    }

    protected $table = 'group_sessions';

    protected $fillable = [
        'group_id',
        'session_template_id',
        'instructor_id',
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
        'session_order' => 'integer',
    ];

    /**
     * Relationships
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function sessionTemplate()
    {
        return $this->belongsTo(SessionTemplate::class);
    }

    public function instructor()
    {
        return $this->belongsTo(\Modules\ACL\Users\Models\User::class, 'instructor_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'group_session_id');
    }

    /**
     * Translation relationships
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\GroupSessionTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(\App\Models\GroupSessionTranslation::class)
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

