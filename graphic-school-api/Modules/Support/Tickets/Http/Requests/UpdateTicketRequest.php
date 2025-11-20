<?php

namespace Modules\Support\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Will be handled by middleware
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:open,in_progress,resolved,closed'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
            'assigned_to' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
        ];
    }
}

