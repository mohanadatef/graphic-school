<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum EnrollmentPaymentStatus: string
{
    use HasValues;

    case NOT_PAID = 'not_paid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
}


