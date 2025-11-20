<?php

namespace Modules\LMS\Courses\Domain\Events;

use App\Support\Events\BaseEvent;

class CourseUpdated extends BaseEvent
{
    public function __construct(
        public int $courseId,
        public array $changes
    ) {
        parent::__construct();
    }
}

