<?php

namespace Modules\LMS\Courses\Http\Requests;

use Modules\LMS\Courses\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'session_count' => ['nullable', 'integer', 'min:1'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'auto_generate_sessions' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'is_hidden' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(CourseStatus::values())],
            'delivery_type' => ['nullable', Rule::in(['on-site', 'online', 'hybrid'])],
            'code' => ['nullable', 'string', 'max:20', 'unique:courses,code'],
            'image' => ['nullable', 'file', 'image', 'max:2048'],
            'default_start_time' => ['nullable', 'date_format:H:i'],
            'default_end_time' => ['nullable', 'date_format:H:i', 'after:default_start_time'],
            'instructors' => ['nullable', 'array'],
            'instructors.*' => ['exists:users,id'],
            'supervisors' => ['nullable', 'array'],
            'supervisors.*' => ['exists:users,id'],
            'translations' => ['nullable', 'array'],
            'translations.*.locale' => ['required', 'in:ar,en'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_description' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $autoGenerate = $this->boolean('auto_generate_sessions', true);

            if (! $autoGenerate) {
                return;
            }

            foreach (['session_count', 'days_of_week', 'start_date'] as $field) {
                if (! $this->filled($field)) {
                    $validator->errors()->add(
                        $field,
                        __('حقل :attribute مطلوب لتوليد الجلسات تلقائياً', ['attribute' => $field])
                    );
                }
            }
        });
    }
}

