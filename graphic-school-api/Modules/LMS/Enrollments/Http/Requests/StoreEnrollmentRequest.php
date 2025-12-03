<?php

namespace Modules\LMS\Enrollments\Http\Requests;

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
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'status' => ['nullable', Rule::in(EnrollmentStatus::values())],
            'can_attend' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

