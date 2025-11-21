<?php

namespace Modules\Core\Localization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['sometimes', 'required', 'string', 'max:255'],
            'locale' => ['sometimes', 'required', 'string', 'in:en,ar'],
            'value' => ['sometimes', 'required', 'string'],
            'group' => ['nullable', 'string', 'max:255'],
        ];
    }
}

