<?php

namespace App\Http\Requests\Admin\Enrollment;

use App\Enums\EnrollmentPaymentStatus;
use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_status' => ['nullable', Rule::in(EnrollmentPaymentStatus::values())],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(EnrollmentStatus::values())],
            'can_attend' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
        ];
    }
}

