<?php

namespace Modules\CMS\Testimonials\Services;

use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Testimonials\Repositories\Interfaces\TestimonialRepositoryInterface;
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
        return $this->testimonialRepository->update($testimonial, $data);
    }

    public function delete(Testimonial $testimonial): void
    {
        $this->testimonialRepository->delete($testimonial);
    }
}

