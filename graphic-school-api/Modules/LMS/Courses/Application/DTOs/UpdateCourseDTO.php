<?php

namespace Modules\LMS\Courses\Application\DTOs;

use App\Support\DTOs\BaseDTO;
use Illuminate\Http\UploadedFile;

class UpdateCourseDTO extends BaseDTO
{
    public ?string $title = null;
    public ?string $code = null;
    public ?int $categoryId = null;
    public ?string $description = null;
    public ?UploadedFile $image = null;
    public ?float $price = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $sessionCount = null;
    public ?array $daysOfWeek = null;
    public ?int $durationWeeks = null;
    public ?int $maxStudents = null;
    public ?bool $autoGenerateSessions = null;
    public ?bool $isPublished = null;
    public ?bool $isHidden = null;
    public ?string $status = null;
    public ?string $defaultStartTime = null;
    public ?string $defaultEndTime = null;
    public ?string $deliveryType = null;
    public ?array $instructors = null;
    public ?array $supervisors = null;
    public bool $regenerateSessions = false;
    public ?array $translations = null;

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null && $value !== false);
    }
}

