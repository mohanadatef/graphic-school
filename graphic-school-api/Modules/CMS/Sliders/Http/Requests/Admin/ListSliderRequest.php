<?php

namespace Modules\CMS\Sliders\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}


