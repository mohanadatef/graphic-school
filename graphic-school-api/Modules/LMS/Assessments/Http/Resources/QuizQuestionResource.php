<?php

namespace Modules\LMS\Assessments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        $isStudent = $request->user() && $request->user()->hasRole('student');
        
        return [
            'id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'question' => $this->question,
            'type' => $this->type,
            'options' => $this->options,
            'correct_answers' => $isStudent ? null : $this->correct_answers, // Hide from students
            'explanation' => $this->explanation,
            'points' => $this->points,
            'order' => $this->order,
        ];
    }
}

