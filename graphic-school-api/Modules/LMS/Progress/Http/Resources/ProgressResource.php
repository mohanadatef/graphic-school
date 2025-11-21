<?php

namespace Modules\LMS\Progress\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'enrollment_id' => $this->enrollment_id,
            'course_id' => $this->course_id,
            'module_id' => $this->module_id,
            'lesson_id' => $this->lesson_id,
            'type' => $this->type,
            'is_completed' => $this->is_completed,
            'progress_percentage' => $this->progress_percentage,
            'time_spent' => $this->time_spent,
            'time_spent_formatted' => $this->formatTime($this->time_spent),
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'last_accessed_at' => $this->last_accessed_at,
        ];
    }

    protected function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours} ساعة و {$minutes} دقيقة";
        }
        return "{$minutes} دقيقة";
    }
}

