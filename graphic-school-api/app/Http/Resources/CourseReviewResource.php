<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'student_id' => $this->student_id,
            'instructor_id' => $this->instructor_id,
            'rating_course' => $this->rating_course,
            'rating_instructor' => $this->rating_instructor,
            'comment' => $this->comment,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
            'student' => $this->whenLoaded('student', fn () => new UserResource($this->student)),
            'instructor' => $this->whenLoaded('instructor', fn () => new UserResource($this->instructor)),
        ];
    }
}

