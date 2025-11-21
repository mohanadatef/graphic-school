<?php

namespace Modules\LMS\Categories\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['sometimes', 'required', 'array'],
            'translations.*' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'translations.required' => 'يجب إدخال اسم التصنيف على الأقل بلغة واحدة',
            'translations.*.required' => 'اسم التصنيف مطلوب',
            'translations.*.max' => 'اسم التصنيف يجب ألا يتجاوز 255 حرف',
        ];
    }
}

