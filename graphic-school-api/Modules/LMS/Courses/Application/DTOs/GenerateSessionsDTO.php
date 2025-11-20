<?php

namespace Modules\LMS\Courses\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class GenerateSessionsDTO extends BaseDTO
{
    public int $sessionCount;
    public array $daysOfWeek;
    public string $startDate;

    public function validate(): void
    {
        if ($this->sessionCount <= 0) {
            throw new \App\Exceptions\DomainException('Session count must be greater than 0', ['session_count' => 'Session count must be greater than 0'], 422);
        }

        if (empty($this->daysOfWeek)) {
            throw new \App\Exceptions\DomainException('Days of week are required', ['days_of_week' => 'Days of week are required'], 422);
        }

        if (empty($this->startDate)) {
            throw new \App\Exceptions\DomainException('Start date is required', ['start_date' => 'Start date is required'], 422);
        }
    }
}

