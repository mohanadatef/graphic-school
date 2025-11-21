<?php

namespace Modules\LMS\Curriculum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:file,link,image,video,document'],
            'file_path' => ['nullable', 'string'],
            'file_name' => ['nullable', 'string'],
            'file_size' => ['nullable', 'integer'],
            'file_type' => ['nullable', 'string'],
            'external_url' => ['nullable', 'string', 'url'],
            'is_downloadable' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}

