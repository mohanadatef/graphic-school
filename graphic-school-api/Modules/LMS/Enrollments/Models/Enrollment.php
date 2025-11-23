<?php

namespace Modules\LMS\Enrollments\Models;

use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'program_id',
        'batch_id',
        'group_id',
        'payment_status',
        'paid_amount',
        'status',
        'can_attend',
        'approved_by',
        'approved_at',
        'note',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'can_attend' => 'bool',
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class);
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Batch::class);
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\EnrollmentLog::class);
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    public function enrollmentLogs()
    {
        return $this->hasMany(\App\Models\EnrollmentLog::class);
    }
}

