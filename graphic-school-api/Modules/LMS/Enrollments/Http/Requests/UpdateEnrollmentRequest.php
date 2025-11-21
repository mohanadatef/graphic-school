<?php

namespace Modules\LMS\Enrollments\Http\Requests;

use Modules\LMS\Enrollments\Enums\EnrollmentPaymentStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(EnrollmentStatus::values())],
            'payment_status' => ['sometimes', Rule::in(EnrollmentPaymentStatus::values())],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'can_attend' => ['nullable', 'boolean'],
        ];
    }
}

