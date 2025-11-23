<?php

namespace Modules\CMS\Testimonials\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\EntityTranslationService;

class TestimonialResource extends JsonResource
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

        $comment = $translationService->getTranslatedField($this->resource, 'comment', $locale, $this->comment);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'company' => $this->company,
            'rating_course' => $this->rating_course,
            'rating_instructor' => $this->rating_instructor,
            'comment' => $comment,
            'avatar_path' => $this->avatar_path,
            'is_featured' => (bool) $this->is_featured,
            'is_approved' => (bool) $this->is_approved,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
        ];
    }
}

