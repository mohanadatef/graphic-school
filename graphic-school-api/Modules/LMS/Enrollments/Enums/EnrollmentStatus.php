<?php

namespace Modules\LMS\Enrollments\Enums;

use App\Enums\Concerns\HasValues;

enum EnrollmentStatus: string
{
    use HasValues;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
}

