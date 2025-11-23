<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'branding.name.display' => ['sometimes', 'string', 'max:255'],
            'branding.logo.default' => ['sometimes', 'image', 'max:2048'],
            'branding.logo.dark' => ['sometimes', 'image', 'max:2048'],
            'branding.logo.favicon' => ['sometimes', 'image', 'max:512'],
            'branding.colors.primary' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'branding.colors.secondary' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'branding.colors.background' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'branding.colors.text' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'branding.fonts.source' => ['sometimes', 'string', 'in:system,custom'],
            'branding.fonts.main' => ['sometimes', 'string', 'max:100'],
            'branding.fonts.headings' => ['sometimes', 'string', 'max:100'],
            'branding.fonts.custom_file' => ['sometimes', 'file', 'mimes:ttf,woff,woff2', 'max:5120'], // 5MB max
            'branding.layout.radius' => ['sometimes', 'string', 'max:50'],
            'branding.layout.shadow' => ['sometimes', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'branding.colors.*.regex' => 'The color must be a valid hex color code (e.g., #3b82f6).',
            'branding.logo.*.image' => 'The logo must be an image file.',
            'branding.logo.*.max' => 'The logo must not be larger than :max kilobytes.',
        ];
    }
}

