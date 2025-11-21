<?php

namespace Modules\ACL\Users\Http\Requests;

use Modules\LMS\Sessions\Enums\SessionStatus;
use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListSessionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'status' => ['nullable', 'string', Rule::in(SessionStatus::values())],
            'attendance_status' => ['nullable', 'string', Rule::in(AttendanceStatus::values())],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}

