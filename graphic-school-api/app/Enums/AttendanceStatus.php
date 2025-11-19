<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum AttendanceStatus: string
{
    use HasValues;

    case PRESENT = 'present';
    case ABSENT = 'absent';
}


