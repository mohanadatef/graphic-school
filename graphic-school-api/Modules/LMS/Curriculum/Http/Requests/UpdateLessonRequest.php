<?php

namespace Modules\LMS\Curriculum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'url'],
            'video_duration' => ['nullable', 'string'],
            'video_provider' => ['nullable', 'string', 'in:youtube,vimeo,self-hosted'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'lesson_type' => ['sometimes', 'string', 'in:video,text,quiz,project,assignment'],
            'is_preview' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}

