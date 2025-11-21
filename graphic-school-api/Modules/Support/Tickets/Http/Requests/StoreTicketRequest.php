<?php

namespace Modules\Support\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CHANGE-006: Store Ticket Request
 */
class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:bug,change_request,new_feature',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ];
    }
}
