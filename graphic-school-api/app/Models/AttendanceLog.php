<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACL\Users\Models\User;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'qr_token_id',
        'student_id',
        'action',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function qrToken()
    {
        return $this->belongsTo(QrToken::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

