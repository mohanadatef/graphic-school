<?php

namespace Modules\LMS\Categories\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['required', 'array'],
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

