<?php

namespace Modules\ACL\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'rating_course' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_instructor' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

