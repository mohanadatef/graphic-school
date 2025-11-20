<?php

namespace Modules\LMS\Courses\Domain\Events;

use App\Support\Events\BaseEvent;

class CourseCreated extends BaseEvent
{
    public function __construct(
        public int $courseId,
        public string $title,
        public string $code,
        public ?int $categoryId = null
    ) {
        parent::__construct();
    }
}

