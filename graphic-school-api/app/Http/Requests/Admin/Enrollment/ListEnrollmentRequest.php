<?php

namespace App\Http\Requests\Admin\Enrollment;

use App\Enums\EnrollmentPaymentStatus;
use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(EnrollmentStatus::values())],
            'payment_status' => ['nullable', 'string', Rule::in(EnrollmentPaymentStatus::values())],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'student_id' => ['nullable', 'integer', 'exists:users,id'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}


