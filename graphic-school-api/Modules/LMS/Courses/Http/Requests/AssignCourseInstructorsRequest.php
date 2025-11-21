<?php

namespace Modules\LMS\Courses\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignCourseInstructorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'instructors' => ['required', 'array'],
            'instructors.*' => ['exists:users,id'],
            'supervisors' => ['nullable', 'array'],
            'supervisors.*' => ['exists:users,id'],
        ];
    }
}

