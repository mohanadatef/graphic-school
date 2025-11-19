<?php

namespace App\Http\Requests\Admin\Session;

use App\Enums\SessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'session_date' => ['sometimes', 'required', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'status' => ['nullable', Rule::in(SessionStatus::values())],
            'note' => ['nullable', 'string'],
        ];
    }
}

