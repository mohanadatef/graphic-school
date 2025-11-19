<?php

namespace App\Services;

use App\Models\Testimonial;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
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

