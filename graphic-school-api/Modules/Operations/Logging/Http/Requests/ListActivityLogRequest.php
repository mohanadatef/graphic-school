<?php

namespace Modules\Operations\Logging\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Will be handled by middleware
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'in:created,updated,deleted'],
            'model_type' => ['nullable', 'string'],
            'model_id' => ['nullable', 'integer'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}

