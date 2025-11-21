<?php

namespace Modules\CMS\Testimonials\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Testimonials\Repositories\Interfaces\TestimonialRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TestimonialRepository extends BaseRepository implements TestimonialRepositoryInterface
{
    /**
     * Make model instance
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Testimonial();
    }

    public function paginateLatest(int $perPage): LengthAwarePaginator
    {
        return $this->query()->latest()->paginate($perPage);
    }

    public function latestApproved(int $limit): Collection
    {
        return $this->query()
            ->where('is_approved', true)
            ->latest()
            ->take($limit)
            ->get();
    }
}

