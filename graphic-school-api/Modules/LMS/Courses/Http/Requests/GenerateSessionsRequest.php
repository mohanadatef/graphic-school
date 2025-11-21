<?php

namespace Modules\LMS\Courses\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateSessionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_count' => ['required', 'integer', 'min:1'],
            'days_of_week' => ['required', 'array', 'min:1'],
            'days_of_week.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'start_date' => ['required', 'date'],
        ];
    }
}

