<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_path' => $this->avatar_path,
            'bio' => $this->bio,
            'address' => $this->address,
            'is_active' => (bool) $this->is_active,
            'average_rating' => $this->when(isset($this->average_rating), (float) $this->average_rating),
            'reviews_count' => $this->when(isset($this->reviews_count), (int) $this->reviews_count),
            'role' => $this->whenLoaded('role', fn () => new RoleResource($this->role)),
        ];
    }
}

