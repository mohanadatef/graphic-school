<?php

namespace Modules\CMS\Sliders\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'subtitle' => $this->subtitle,
            'button_text' => $this->button_text,
            'button_url' => $this->button_url,
            'image_path' => $this->image_path,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
        ];
    }
}
