<?php

namespace Modules\LMS\Certificates\Services;

use Modules\LMS\Certificates\Models\Certificate;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Support\Facades\DB;

class CertificateService
{
    public function issueCertificate(int $enrollmentId): Certificate
    {
        return DB::transaction(function () use ($enrollmentId) {
            $enrollment = Enrollment::with('course', 'student')->findOrFail($enrollmentId);

            // Check if enrollment is completed
            if ($enrollment->progress_percentage < 100) {
                throw new \Exception('يجب إكمال الكورس بنسبة 100% للحصول على الشهادة');
            }

            // Check if certificate already exists
            $existingCertificate = Certificate::where('enrollment_id', $enrollmentId)->first();
            if ($existingCertificate) {
                return $existingCertificate;
            }

            // Check if course has certificate enabled
            if (!$enrollment->course->has_certificate) {
                throw new \Exception('هذا الكورس لا يوفر شهادة');
            }

            // Create certificate
            $certificate = Certificate::create([
                'course_id' => $enrollment->course_id,
                'student_id' => $enrollment->student_id,
                'enrollment_id' => $enrollmentId,
                'issued_date' => now(),
            ]);

            // Mark enrollment as certificate issued
            $enrollment->update(['certificate_issued' => true]);

            // Update course completion count
            Course::where('id', $enrollment->course_id)
                ->increment('completion_count');

            return $certificate;
        });
    }

    public function verifyCertificate(string $verificationCode): ?Certificate
    {
        return Certificate::where('verification_code', $verificationCode)
            ->where('is_verified', true)
            ->with(['course', 'student'])
            ->first();
    }

    public function getStudentCertificates(int $studentId): array
    {
        $certificates = Certificate::where('student_id', $studentId)
            ->with(['course', 'enrollment'])
            ->orderBy('issued_date', 'desc')
            ->get();

        return $certificates->map(function ($certificate) {
            return [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'course_title' => $certificate->course->title,
                'issued_date' => $certificate->issued_date->format('Y-m-d'),
                'verification_code' => $certificate->verification_code,
                'verification_url' => $certificate->getVerificationUrl(),
                'pdf_path' => $certificate->pdf_path,
            ];
        })->toArray();
    }
}

