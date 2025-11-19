<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'student_id' => $this->student_id,
            'course_id' => $this->course_id,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'paid_amount' => $this->paid_amount,
            'can_attend' => (bool) $this->can_attend,
            'approved_by' => $this->approved_by,
            'approved_at' => optional($this->approved_at)?->toDateTimeString(),
            'created_at' => optional($this->created_at)?->toDateTimeString(),
            'student' => $this->whenLoaded('student', fn () => new UserResource($this->student)),
            'course' => $this->whenLoaded('course', fn () => new CourseResource($this->course)),
        ];
    }
}

