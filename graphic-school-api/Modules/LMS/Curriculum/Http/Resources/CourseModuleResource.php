<?php

namespace Modules\LMS\Curriculum\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'is_published' => $this->is_published,
            'is_preview' => $this->is_preview,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'lessons_count' => $this->when(isset($this->lessons_count), $this->lessons_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

