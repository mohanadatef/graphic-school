<?php

namespace Modules\LMS\Curriculum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Protected by middleware
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'is_preview' => ['sometimes', 'boolean'],
            'translations' => ['nullable', 'array'],
            'translations.*.locale' => ['required', 'in:ar,en'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
        ];
    }
}

