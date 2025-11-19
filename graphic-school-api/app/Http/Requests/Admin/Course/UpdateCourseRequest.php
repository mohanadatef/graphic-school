<?php

namespace App\Http\Requests\Admin\Course;

use App\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'session_count' => ['nullable', 'integer', 'min:1'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'auto_generate_sessions' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'is_hidden' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(CourseStatus::values())],
            'code' => ['nullable', 'string', 'max:20', 'unique:courses,code,' . $courseId],
            'image' => ['nullable', 'file', 'image', 'max:2048'],
            'default_start_time' => ['nullable', 'date_format:H:i'],
            'default_end_time' => ['nullable', 'date_format:H:i', 'after:default_start_time'],
            'instructors' => ['nullable', 'array'],
            'instructors.*' => ['exists:users,id'],
            'supervisors' => ['nullable', 'array'],
            'supervisors.*' => ['exists:users,id'],
            'regenerate_sessions' => ['nullable', 'boolean'],
        ];
    }
}

