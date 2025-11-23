<?php

namespace Modules\CMS\Sliders\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\EntityTranslationService;

class SliderResource extends JsonResource
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
        $subtitle = $translationService->getTranslatedField($this->resource, 'subtitle', $locale, $this->subtitle);
        $buttonText = $translationService->getTranslatedField($this->resource, 'button_text', $locale, $this->button_text);

        return [
            'id' => $this->id,
            'title' => $title,
            'subtitle' => $subtitle,
            'button_text' => $buttonText,
            'button_url' => $this->button_url,
            'image_path' => $this->image_path,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
        ];
    }
}
