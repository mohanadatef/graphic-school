<?php

namespace Modules\LMS\Courses\Enums;

use App\Enums\Concerns\HasValues;

enum CourseStatus: string
{
    use HasValues;

    case DRAFT = 'draft';
    case UPCOMING = 'upcoming';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
}

