<?php

namespace Modules\CMS\Testimonials\Services;

use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Testimonials\Repositories\Interfaces\TestimonialRepositoryInterface;
use App\Services\EntityTranslationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TestimonialService
{
    public function __construct(private TestimonialRepositoryInterface $testimonialRepository)
    {
    }

    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        return $this->testimonialRepository->paginateLatest($perPage);
    }

    public function update(Testimonial $testimonial, array $data): Testimonial
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $testimonial = $this->testimonialRepository->update($testimonial, $data);

        // Save translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($testimonial, $translations);
        }

        return $testimonial;
    }

    public function delete(Testimonial $testimonial): void
    {
        // Delete translations
        $translationService = app(EntityTranslationService::class);
        $translationService->deleteTranslations($testimonial);

        $this->testimonialRepository->delete($testimonial);
    }
}

