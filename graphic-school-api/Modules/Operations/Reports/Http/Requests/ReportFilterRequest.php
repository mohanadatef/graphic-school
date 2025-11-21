<?php

namespace Modules\Operations\Reports\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Should be protected by middleware
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'status' => ['sometimes', 'string'],
            'instructor_id' => ['sometimes', 'integer', 'exists:users,id'],
            'course_id' => ['sometimes', 'integer', 'exists:courses,id'],
            'from_date' => ['sometimes', 'date'],
            'to_date' => ['sometimes', 'date', 'after_or_equal:from_date'],
        ];
    }
}

