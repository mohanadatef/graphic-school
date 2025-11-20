<?php

namespace Modules\LMS\Enrollments\Http\Requests;

use Modules\LMS\Enrollments\Enums\EnrollmentPaymentStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'status' => ['nullable', Rule::in(EnrollmentStatus::values())],
            'payment_status' => ['nullable', Rule::in(EnrollmentPaymentStatus::values())],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

