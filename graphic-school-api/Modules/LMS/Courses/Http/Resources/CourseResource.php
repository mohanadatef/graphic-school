<?php

namespace Modules\LMS\Courses\Http\Resources;

use Modules\LMS\Categories\Http\Resources\CategoryResource;
use Modules\ACL\Users\Http\Resources\UserResource;
use Modules\LMS\Sessions\Http\Resources\SessionResource;
use Modules\LMS\Enrollments\Http\Resources\EnrollmentResource;
use Modules\LMS\CourseReviews\Http\Resources\CourseReviewResource;
use Modules\CMS\Testimonials\Http\Resources\TestimonialResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'price' => $this->price,
            'start_date' => optional($this->start_date)?->toDateString(),
            'end_date' => optional($this->end_date)?->toDateString(),
            'session_count' => $this->session_count,
            'days_of_week' => $this->days_of_week,
            'duration_weeks' => $this->duration_weeks,
            'max_students' => $this->max_students,
            'auto_generate_sessions' => (bool) $this->auto_generate_sessions,
            'is_published' => (bool) $this->is_published,
            'is_hidden' => (bool) $this->is_hidden,
            'status' => $this->status,
            'delivery_type' => $this->delivery_type ?? 'on-site',
            'default_start_time' => $this->default_start_time,
            'default_end_time' => $this->default_end_time,
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'instructors' => UserResource::collection($this->whenLoaded('instructors')),
            'sessions' => SessionResource::collection($this->whenLoaded('sessions')),
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'reviews' => CourseReviewResource::collection($this->whenLoaded('reviews')),
            'testimonials' => TestimonialResource::collection($this->whenLoaded('testimonials')),
            'reviews_summary' => $this->when(isset($this->reviews_summary), $this->reviews_summary),
            'enrollment_status' => $this->when(isset($this->enrollment_status), $this->enrollment_status),
            'next_session' => $this->when($this->relationLoaded('sessions'), fn () => $this->nextSession()),
        ];
    }
}

