<?php

namespace Modules\ACL\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}

