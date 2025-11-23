<?php

namespace Modules\CMS\Testimonials\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_approved' => ['required', 'boolean'],
            'translations' => ['nullable', 'array'],
            'translations.*.locale' => ['required', 'in:ar,en'],
            'translations.*.comment' => ['nullable', 'string'],
        ];
    }
}

