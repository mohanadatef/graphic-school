<?php

namespace Modules\LMS\Enrollments\Http\Resources;

use Modules\ACL\Users\Http\Resources\UserResource;
use Modules\LMS\Courses\Http\Resources\CourseResource;
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
            'total_amount' => $this->total_amount,
            'can_attend' => (bool) $this->can_attend,
            'approved_by' => $this->approved_by,
            'approved_at' => optional($this->approved_at)?->toDateTimeString(),
            'created_at' => optional($this->created_at)?->toDateTimeString(),
            'student' => $this->whenLoaded('student', function () {
                return $this->student ? new UserResource($this->student) : null;
            }),
            'course' => $this->whenLoaded('course', function () {
                return $this->course ? new CourseResource($this->course) : null;
            }),
        ];
    }
}

