<?php

namespace Modules\LMS\Sessions\Http\Requests;

use Modules\LMS\Sessions\Enums\SessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'session_date' => ['sometimes', 'required', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'status' => ['nullable', Rule::in(SessionStatus::values())],
            'note' => ['nullable', 'string'],
            'student_comment' => ['nullable', 'string'],
            'student_file' => ['nullable', 'file', 'max:10240'],
            'instructor_comment' => ['nullable', 'string'],
            'supervisor_comment' => ['nullable', 'string'],
        ];
    }
}

