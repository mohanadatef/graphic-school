<?php

namespace Modules\ACL\Users\Http\Requests;

use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'exists:sessions,id'],
            'records' => ['required', 'array'],
            'records.*.student_id' => ['required', 'exists:users,id'],
            'records.*.status' => ['required', Rule::in(AttendanceStatus::values())],
            'records.*.note' => ['nullable', 'string'],
        ];
    }
}

