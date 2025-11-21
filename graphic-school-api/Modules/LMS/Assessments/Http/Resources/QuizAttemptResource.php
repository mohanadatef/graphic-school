<?php

namespace Modules\LMS\Assessments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'quiz_id' => $this->quiz_id,
            'enrollment_id' => $this->enrollment_id,
            'answers' => $this->answers,
            'score' => $this->score,
            'total_points' => $this->total_points,
            'percentage' => $this->percentage,
            'is_passed' => $this->is_passed,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'time_taken' => $this->time_taken,
            'quiz' => new QuizResource($this->whenLoaded('quiz')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

