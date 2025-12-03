<?php

namespace Modules\LMS\Enrollments\Http\Requests;

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
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'can_attend' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

