<?php

namespace Modules\LMS\Curriculum\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\EntityTranslationService;

class LessonResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = $request instanceof Request ? ($request->attributes->get('locale') ?? app()->getLocale()) : app()->getLocale();
        $translationService = app(EntityTranslationService::class);

        $title = $translationService->getTranslatedField($this->resource, 'title', $locale, $this->title);
        $description = $translationService->getTranslatedField($this->resource, 'description', $locale, $this->description);
        $content = $translationService->getTranslatedField($this->resource, 'content', $locale, $this->content);

        return [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'video_url' => $this->video_url,
            'video_duration' => $this->video_duration,
            'video_provider' => $this->video_provider,
            'order' => $this->order,
            'lesson_type' => $this->lesson_type,
            'is_preview' => $this->is_preview,
            'is_published' => $this->is_published,
            'resources' => LessonResourceResource::collection($this->whenLoaded('resources')),
            'quiz' => $this->whenLoaded('quiz', function () {
                return $this->quiz ? [
                    'id' => $this->quiz->id,
                    'title' => $this->quiz->title,
                ] : null;
            }),
            'has_quiz' => $this->relationLoaded('quiz') && $this->quiz !== null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

