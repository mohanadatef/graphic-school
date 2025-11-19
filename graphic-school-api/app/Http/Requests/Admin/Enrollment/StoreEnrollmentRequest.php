<?php

namespace App\Http\Requests\Admin\Enrollment;

use App\Enums\EnrollmentPaymentStatus;
use App\Enums\EnrollmentStatus;
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
            'student_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'payment_status' => ['required', Rule::in(EnrollmentPaymentStatus::values())],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(EnrollmentStatus::values())],
            'can_attend' => ['nullable', 'boolean'],
        ];
    }
}

