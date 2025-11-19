<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
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
}
