<?php

namespace Database\Factories;

use App\Models\Media;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-002: Media Factory
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $mimeTypes = [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf', 'application/msword',
            'video/mp4',
        ];
        $mimeType = fake()->randomElement($mimeTypes);
        $type = str_starts_with($mimeType, 'image/') ? 'image' : 
                (str_starts_with($mimeType, 'video/') ? 'video' : 'document');

        return [
            'name' => fake()->word() . '.' . fake()->fileExtension(),
            'file_name' => fake()->uuid() . '.' . fake()->fileExtension(),
            'file_path' => 'media/' . fake()->uuid() . '.' . fake()->fileExtension(),
            'mime_type' => $mimeType,
            'file_size' => fake()->numberBetween(1000, 10000000),
            'disk' => 'public',
            'type' => $type,
            'alt_text' => fake()->sentence(3),
            'description' => fake()->paragraph(1),
            'uploaded_by' => User::factory(),
            'metadata' => $type === 'image' ? [
                'width' => fake()->numberBetween(100, 2000),
                'height' => fake()->numberBetween(100, 2000),
            ] : null,
        ];
    }
}
