<?php

namespace Modules\LMS\Assessments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['sometimes', 'integer', 'min:1'],
            'show_results' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'questions' => ['sometimes', 'array', 'min:1'],
            'questions.*.question' => ['required_with:questions', 'string'],
            'questions.*.type' => ['required_with:questions', 'string', 'in:multiple_choice,true_false,short_answer,essay'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.correct_answers' => ['required_with:questions', 'array'],
            'questions.*.explanation' => ['nullable', 'string'],
            'questions.*.points' => ['sometimes', 'integer', 'min:1'],
            'questions.*.order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}

