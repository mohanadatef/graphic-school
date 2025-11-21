<?php

namespace Modules\LMS\Courses\Domain\Services;

use Illuminate\Support\Str;

/**
 * Domain Service for generating course slugs and codes
 * Follows Single Responsibility Principle - only responsible for slug/code generation
 */
class CourseSlugGeneratorService
{
    /**
     * Generate slug from title
     */
    public function generateSlug(string $title): string
    {
        return Str::slug($title);
    }

    /**
     * Generate course code
     */
    public function generateCode(?string $customCode = null): string
    {
        return $customCode ?? 'GS-' . Str::upper(Str::random(6));
    }
}

