<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'session_id' => $this->session_id,
            'student_id' => $this->student_id,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
            'session' => $this->whenLoaded('session', fn () => new SessionResource($this->session)),
            'student' => $this->whenLoaded('student', fn () => new UserResource($this->student)),
        ];
    }
}

