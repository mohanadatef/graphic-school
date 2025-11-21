<?php

namespace Modules\LMS\Assessments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'module_id' => ['nullable', 'integer', 'exists:course_modules,id'],
            'lesson_id' => ['nullable', 'integer', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['sometimes', 'integer', 'min:1'],
            'show_results' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string'],
            'questions.*.type' => ['required', 'string', 'in:multiple_choice,true_false,short_answer,essay'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.correct_answers' => ['required', 'array'],
            'questions.*.explanation' => ['nullable', 'string'],
            'questions.*.points' => ['sometimes', 'integer', 'min:1'],
            'questions.*.order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}

