<?php

namespace Modules\LMS\Curriculum\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'lesson_id' => $this->lesson_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'file_path' => $this->file_path,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'external_url' => $this->external_url,
            'is_downloadable' => $this->is_downloadable,
            'download_count' => $this->download_count,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

