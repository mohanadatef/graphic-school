<?php

namespace Modules\LMS\Attendance\Models;

use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'session_id',
        'student_id',
        'status',
        'note',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

