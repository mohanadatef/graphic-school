<?php

namespace Modules\LMS\Certificates\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'student_id' => $this->student_id,
            'enrollment_id' => $this->enrollment_id,
            'certificate_number' => $this->certificate_number,
            'template_path' => $this->template_path,
            'pdf_path' => $this->pdf_path,
            'issued_date' => $this->issued_date?->format('Y-m-d'),
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'is_verified' => $this->is_verified,
            'verification_code' => $this->verification_code,
            'verification_url' => $this->getVerificationUrl(),
            'course' => $this->whenLoaded('course'),
            'student' => $this->whenLoaded('student'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

