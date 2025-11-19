<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
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
            'role' => $this->role,
            'company' => $this->company,
            'rating_course' => $this->rating_course,
            'rating_instructor' => $this->rating_instructor,
            'comment' => $this->comment,
            'avatar_path' => $this->avatar_path,
            'is_featured' => (bool) $this->is_featured,
            'is_approved' => (bool) $this->is_approved,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
        ];
    }
}


