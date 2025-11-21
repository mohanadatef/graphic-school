<?php

namespace Modules\LMS\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'session_id' => ['nullable', 'integer', 'exists:sessions,id'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}

