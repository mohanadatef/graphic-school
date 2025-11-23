<?php

namespace Modules\LMS\Curriculum\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\EntityTranslationService;

class CourseModuleResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = $request instanceof Request ? ($request->attributes->get('locale') ?? app()->getLocale()) : app()->getLocale();
        $translationService = app(EntityTranslationService::class);

        $title = $translationService->getTranslatedField($this->resource, 'title', $locale, $this->title);
        $description = $translationService->getTranslatedField($this->resource, 'description', $locale, $this->description);

        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $title,
            'description' => $description,
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

