<?php

namespace Modules\LMS\Assessments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'course_id' => $this->course_id,
            'module_id' => $this->module_id,
            'lesson_id' => $this->lesson_id,
            'enrollment_id' => $this->enrollment_id,
            'title' => $this->title,
            'description' => $this->description,
            'files' => $this->files,
            'submission_note' => $this->submission_note,
            'status' => $this->status,
            'score' => $this->score,
            'instructor_feedback' => $this->instructor_feedback,
            'reviewed_by' => $this->reviewed_by,
            'submitted_at' => $this->submitted_at,
            'reviewed_at' => $this->reviewed_at,
            'course' => $this->whenLoaded('course'),
            'module' => $this->whenLoaded('module'),
            'lesson' => $this->whenLoaded('lesson'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

