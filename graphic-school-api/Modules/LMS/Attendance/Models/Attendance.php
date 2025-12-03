<?php

namespace Modules\LMS\Attendance\Models;

use Modules\LMS\Sessions\Models\GroupSession;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'group_session_id',
        'student_id',
        'status',
        'note',
        'marked_by',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function groupSession()
    {
        return $this->belongsTo(GroupSession::class);
    }

    /**
     * Alias for backward compatibility
     */
    public function session()
    {
        return $this->groupSession();
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}

