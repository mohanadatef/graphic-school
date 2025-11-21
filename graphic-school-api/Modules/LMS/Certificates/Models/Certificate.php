<?php

namespace Modules\LMS\Certificates\Models;

use Modules\LMS\Courses\Models\Course;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'course_id',
        'student_id',
        'enrollment_id',
        'certificate_number',
        'template_path',
        'pdf_path',
        'issued_date',
        'expiry_date',
        'is_verified',
        'verification_code',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
        'is_verified' => 'bool',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = self::generateCertificateNumber();
            }
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = self::generateVerificationCode();
            }
            if (empty($certificate->issued_date)) {
                $certificate->issued_date = now();
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    protected static function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . strtoupper(Str::random(8)) . '-' . date('Y');
        } while (self::where('certificate_number', $number)->exists());

        return $number;
    }

    protected static function generateVerificationCode(): string
    {
        do {
            $code = Str::random(16);
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    public function getVerificationUrl(): string
    {
        return url("/certificates/verify/{$this->verification_code}");
    }
}

