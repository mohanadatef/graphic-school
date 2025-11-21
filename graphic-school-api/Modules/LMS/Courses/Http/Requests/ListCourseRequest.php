<?php

namespace Modules\LMS\Courses\Http\Requests;

use App\Support\Table\TableRequest;

class ListCourseRequest extends TableRequest
{
    protected function customRules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'delivery_type' => ['nullable', 'string', 'in:on_site,online,hybrid'],
        ];
    }
}
