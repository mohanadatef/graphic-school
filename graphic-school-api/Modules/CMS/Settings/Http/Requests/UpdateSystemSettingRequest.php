<?php

namespace Modules\CMS\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Will be handled by middleware
    }

    public function rules(): array
    {
        return [
            'site_name' => ['sometimes', 'string', 'max:255'],
            'logo' => ['sometimes', 'image', 'max:2048'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:30'],
            'address' => ['sometimes', 'string', 'max:500'],
            'colors' => ['sometimes', 'array'],
            'colors.website' => ['sometimes', 'array'],
            'colors.dashboard' => ['sometimes', 'array'],
            'section' => ['sometimes', 'string'],
            'visible' => ['sometimes', 'boolean'],
            'default_language' => ['sometimes', 'string', 'in:ar,en'],
            'available_languages' => ['sometimes', 'array'],
            'default_currency' => ['sometimes', 'string', 'in:EGP,SAR,AED,KWD,BHD,OMR,QAR,USD'],
            'currency_symbol' => ['sometimes', 'string', 'max:10'],
            'currency_position' => ['sometimes', 'string', 'in:before,after'],
        ];
    }
}

