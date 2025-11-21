<?php

namespace Modules\LMS\Courses\Domain\Events;

use App\Support\Events\BaseEvent;

class CourseDeleted extends BaseEvent
{
    public function __construct(
        public int $courseId,
        public string $title
    ) {
        parent::__construct();
    }
}

