<?php

namespace Modules\ACL\Users\Http\Resources;

use Modules\ACL\Roles\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_path' => $this->avatar_path,
            'bio' => $this->bio,
            'address' => $this->address,
            'is_active' => (bool) $this->is_active,
            'average_rating' => $this->when(isset($this->average_rating), (float) $this->average_rating),
            'reviews_count' => $this->when(isset($this->reviews_count), (int) $this->reviews_count),
            'courses_count' => $this->when(isset($this->courses_count), (int) $this->courses_count),
            'students_count' => $this->when(isset($this->students_count), (int) $this->students_count),
            'role' => $this->whenLoaded('role', fn () => new RoleResource($this->role)),
            'instructor_courses' => $this->whenLoaded('instructorCourses', function () {
                return $this->instructorCourses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'slug' => $course->slug,
                        'category' => $course->category ? [
                            'id' => $course->category->id,
                            'name' => $course->category->name,
                        ] : null,
                        'price' => $course->price,
                        'session_count' => $course->session_count,
                        'start_date' => optional($course->start_date)?->toDateString(),
                    ];
                });
            }),
        ];
    }
}

