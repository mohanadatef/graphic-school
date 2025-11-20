<?php

namespace Modules\LMS\Courses\Application\DTOs;

use App\Support\DTOs\BaseDTO;
use Illuminate\Http\UploadedFile;

class CreateCourseDTO extends BaseDTO
{
    public string $title;
    public ?string $code = null;
    public int $categoryId;
    public string $description;
    public ?UploadedFile $image = null;
    public float $price;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $sessionCount;
    public array $daysOfWeek;
    public int $durationWeeks;
    public int $maxStudents;
    public bool $autoGenerateSessions = true;
    public bool $isPublished = false;
    public bool $isHidden = false;
    public string $status;
    public ?string $defaultStartTime = null;
    public ?string $defaultEndTime = null;
    public string $deliveryType = 'on_site';
    public array $instructors = [];
    public array $supervisors = [];

    public function validate(): void
    {
        if (empty($this->title)) {
            throw new \App\Exceptions\DomainException('Title is required', ['title' => 'Title is required'], 422);
        }

        if (empty($this->categoryId)) {
            throw new \App\Exceptions\DomainException('Category is required', ['category_id' => 'Category is required'], 422);
        }

        if ($this->sessionCount <= 0) {
            throw new \App\Exceptions\DomainException('Session count must be greater than 0', ['session_count' => 'Session count must be greater than 0'], 422);
        }

        if (empty($this->daysOfWeek)) {
            throw new \App\Exceptions\DomainException('Days of week are required', ['days_of_week' => 'Days of week are required'], 422);
        }
    }
}

