<?php

namespace Modules\LMS\Courses\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class AssignInstructorsDTO extends BaseDTO
{
    public array $instructors = [];
    public array $supervisors = [];

    public function validate(): void
    {
        // Additional validation if needed
    }
}

