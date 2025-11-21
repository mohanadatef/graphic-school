<?php

namespace Modules\Support\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CHANGE-006: Update Ticket Request
 */
class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => 'sometimes|in:bug,change_request,new_feature',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:5000',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }
}
