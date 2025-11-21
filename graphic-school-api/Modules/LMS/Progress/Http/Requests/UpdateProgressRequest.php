<?php

namespace Modules\LMS\Progress\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'time_spent' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}

