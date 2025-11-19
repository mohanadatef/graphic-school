<?php

namespace App\Repositories\Eloquent;

use App\Models\Testimonial;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TestimonialRepository extends BaseRepository implements TestimonialRepositoryInterface
{
    public function __construct(Testimonial $model)
    {
        parent::__construct($model);
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


