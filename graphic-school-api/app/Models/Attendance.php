<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'session_id',
        'student_id',
        'status',
        'timestamp',
        'notes',
        'marked_by',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}

