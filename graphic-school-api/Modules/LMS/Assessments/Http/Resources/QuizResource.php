<?php

namespace Modules\LMS\Assessments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'module_id' => $this->module_id,
            'lesson_id' => $this->lesson_id,
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
            'passing_score' => $this->passing_score,
            'max_attempts' => $this->max_attempts,
            'show_results' => $this->show_results,
            'is_published' => $this->is_published,
            'questions' => QuizQuestionResource::collection($this->whenLoaded('questions')),
            'questions_count' => $this->when(isset($this->questions_count), $this->questions_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

