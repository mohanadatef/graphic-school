<?php

namespace App\Repositories\Contracts;

use App\Models\Slider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SliderRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator;

    public function activeSorted(): Collection;

    public function findById(int $id): ?Slider;
}


