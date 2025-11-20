<?php

namespace Modules\Operations\Dashboard\Http\Requests;

use Modules\LMS\Courses\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardFilterRequest extends FormRequest
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
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'string', Rule::in(CourseStatus::values())],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}

