<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\ACL\Users\Models\User;

class EnrollmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'action',
        'admin_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

