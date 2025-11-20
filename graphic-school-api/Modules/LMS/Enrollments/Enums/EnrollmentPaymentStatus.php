<?php

namespace Modules\LMS\Enrollments\Enums;

use App\Enums\Concerns\HasValues;

enum EnrollmentPaymentStatus: string
{
    use HasValues;

    case NOT_PAID = 'not_paid';
    case PARTIAL = 'partial';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case REJECTED = 'rejected';
}

