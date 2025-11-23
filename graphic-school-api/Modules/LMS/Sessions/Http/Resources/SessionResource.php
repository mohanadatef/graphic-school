<?php

namespace Modules\LMS\Sessions\Http\Resources;

use Modules\LMS\Courses\Http\Resources\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\EntityTranslationService;

class SessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->attributes->get('locale') ?? app()->getLocale();
        $translationService = app(EntityTranslationService::class);

        $title = $translationService->getTranslatedField($this->resource, 'title', $locale, $this->title);
        $note = $translationService->getTranslatedField($this->resource, 'note', $locale, $this->note);

        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $title,
            'session_order' => $this->session_order,
            'session_date' => optional($this->session_date)->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'note' => $note,
            'student_comment' => $this->student_comment,
            'student_file_path' => $this->student_file_path,
            'instructor_comment' => $this->instructor_comment,
            'supervisor_comment' => $this->supervisor_comment,
            'course' => $this->whenLoaded('course', fn () => new CourseResource($this->course)),
        ];
    }
}

