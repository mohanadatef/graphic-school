<?php

namespace Modules\ACL\Users\Http\Requests;

use App\Support\Table\TableRequest;

class ListUserRequest extends TableRequest
{
    /**
     * @return array<string, array<int, string>|string>
     */
    protected function customRules(): array
    {
        return [
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ];
    }
}
