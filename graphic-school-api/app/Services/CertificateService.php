<?php

namespace App\Services;

use App\Models\CertificateTemplate;
use Modules\LMS\Certificates\Models\Certificate;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class CertificateService
{

    /**
     * Issue certificate for enrollment
     */
    public function issueCertificate(int $enrollmentId, ?int $templateId = null): Certificate
    {
        $enrollment = Enrollment::with(['student', 'course'])->findOrFail($enrollmentId);

        if (!$enrollment->course_id) {
            throw new \Exception('Enrollment must be for a course to issue certificate');
        }

        $template = $templateId 
            ? CertificateTemplate::findOrFail($templateId)
            : CertificateTemplate::where('is_active', true)->first();

        if (!$template) {
            throw new \Exception('No active certificate template found');
        }

        // Generate verification code
        $verificationCode = $this->generateVerificationCode();

        $certificate = Certificate::create([
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'certificate_template_id' => $template->id,
            'verification_code' => $verificationCode,
            'issued_at' => now(),
        ]);

        // Generate PDF (placeholder - will be implemented with DomPDF or Browsershot)
        // $this->generateCertificatePDF($certificate, $template, $enrollment);

        return $certificate;
    }

    /**
     * Generate verification code
     */
    protected function generateVerificationCode(): string
    {
        do {
            $code = 'CERT-' . strtoupper(Str::random(12));
        } while (Certificate::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Verify certificate
     */
    public function verifyCertificate(string $verificationCode): ?Certificate
    {
        return Certificate::where('verification_code', $verificationCode)
            ->with(['student', 'course'])
            ->first();
    }

    /**
     * Generate certificate PDF (placeholder)
     */
    protected function generateCertificatePDF(Certificate $certificate, CertificateTemplate $template, Enrollment $enrollment): void
    {
        // TODO: Implement PDF generation using DomPDF or Browsershot
        // This will use the template layout, branding, and fonts
        // Placeholders: {student_name}, {course_name}, {issue_date}, {academy_name}, {academy_logo}
    }
}

