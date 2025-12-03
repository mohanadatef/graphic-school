<?php

namespace Modules\LMS\Certificates\Services;

use Modules\LMS\Certificates\Models\Certificate;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    /**
     * Issue certificate for a student/course/group
     * Admin or Instructor can issue certificates
     */
    public function issueCertificate(
        int $studentId,
        int $courseId,
        ?int $groupId = null,
        ?int $instructorId = null
    ): Certificate {
        return DB::transaction(function () use ($studentId, $courseId, $groupId, $instructorId) {
            // Verify enrollment exists and is approved
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('status', 'approved')
                ->firstOrFail();

            // Use enrollment's group if group_id not provided
            if (!$groupId && $enrollment->group_id) {
                $groupId = $enrollment->group_id;
            }

            // Check if certificate already exists
            $existingCertificate = Certificate::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('group_id', $groupId)
                ->first();

            if ($existingCertificate) {
                return $existingCertificate;
            }

            // Create certificate
            $certificate = Certificate::create([
                'course_id' => $courseId,
                'group_id' => $groupId,
                'student_id' => $studentId,
                'instructor_id' => $instructorId,
                'enrollment_id' => $enrollment->id,
                'issued_date' => now(),
            ]);

            // Generate QR code for verification
            $this->generateQrCode($certificate);

            return $certificate->load(['course', 'group', 'student', 'instructor']);
        });
    }

    /**
     * Generate QR code for certificate verification
     */
    protected function generateQrCode(Certificate $certificate): void
    {
        $verificationUrl = route('certificate.verify', ['code' => $certificate->verification_code]);
        
        // Generate QR code as base64
        $qrCode = QrCode::format('png')
            ->size(200)
            ->generate($verificationUrl);
        
        // Store QR code (as base64 or save to file)
        $certificate->qr_code = base64_encode($qrCode);
        $certificate->save();
    }

    /**
     * Verify certificate by verification code
     */
    public function verifyCertificate(string $verificationCode): ?Certificate
    {
        return Certificate::where('verification_code', $verificationCode)
            ->where('is_verified', true)
            ->with(['course', 'group', 'student', 'instructor'])
            ->first();
    }

    /**
     * Get all certificates for a student
     */
    public function getStudentCertificates(int $studentId)
    {
        return Certificate::where('student_id', $studentId)
            ->with(['course', 'group', 'instructor'])
            ->orderBy('issued_date', 'desc')
            ->get();
    }

    /**
     * Get all certificates (Admin view)
     */
    public function getAllCertificates(array $filters = [])
    {
        $query = Certificate::with(['course', 'group', 'student', 'instructor']);

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        return $query->orderBy('issued_date', 'desc')->paginate($filters['per_page'] ?? 15);
    }
}
