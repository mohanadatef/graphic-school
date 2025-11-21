<?php

namespace Modules\CMS\PublicSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'only_upcoming' => ['nullable', 'boolean'],
        ];
    }
}

