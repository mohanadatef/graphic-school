<?php

namespace Modules\LMS\Sessions\Enums;

use App\Enums\Concerns\HasValues;

enum SessionStatus: string
{
    use HasValues;

    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}

