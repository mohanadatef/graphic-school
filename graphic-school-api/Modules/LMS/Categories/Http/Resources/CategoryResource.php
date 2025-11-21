<?php

namespace Modules\LMS\Categories\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', app()->getLocale());
        
        // Get name for requested locale
        // First try to get from loaded relations
        $translation = null;
        if ($this->relationLoaded('translations')) {
            $translation = $this->translations->firstWhere('locale', $locale);
        }
        
        // If not found in loaded relations, query directly
        if (!$translation) {
            $translation = $this->translations()->where('locale', $locale)->first();
        }
        
        // Fallback to first available translation
        if (!$translation && $this->relationLoaded('translations') && $this->translations->isNotEmpty()) {
            $translation = $this->translations->first();
        } elseif (!$translation) {
            $translation = $this->translations()->first();
        }
        
        $name = $translation?->name ?? '';

        return [
            'id' => $this->id,
            'name' => $name,
            'localized_name' => $name,
            'is_active' => (bool) $this->is_active,
            'translations' => $this->when(
                $request->has('include_translations') || $request->query('include_translations'),
                function () {
                    if ($this->relationLoaded('translations')) {
                        return $this->translations->mapWithKeys(fn($t) => [$t->locale => $t->name]);
                    }
                    return $this->translations()->get()->mapWithKeys(fn($t) => [$t->locale => $t->name]);
                }
            ),
        ];
    }
}

