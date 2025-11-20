<?php

namespace Modules\LMS\Sessions\Http\Requests;

use Modules\LMS\Sessions\Enums\SessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListSessionRequest extends FormRequest
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
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'status' => ['nullable', 'string', Rule::in(SessionStatus::values())],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}

