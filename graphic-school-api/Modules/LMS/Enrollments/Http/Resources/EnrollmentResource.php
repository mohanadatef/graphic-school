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
            'group_id' => $this->group_id,
            'status' => $this->status,
            'can_attend' => (bool) $this->can_attend,
            'approved_by' => $this->approved_by,
            'approved_at' => optional($this->approved_at)?->toDateTimeString(),
            'note' => $this->note,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
            'updated_at' => optional($this->updated_at)?->toDateTimeString(),
            'student' => $this->whenLoaded('student', function () {
                return $this->student ? new UserResource($this->student) : null;
            }),
            'course' => $this->whenLoaded('course', function () {
                return $this->course ? new CourseResource($this->course) : null;
            }),
            'group' => $this->whenLoaded('group', function () {
                return $this->group ? [
                    'id' => $this->group->id,
                    'name' => $this->group->name,
                    'code' => $this->group->code,
                ] : null;
            }),
        ];
    }
}

