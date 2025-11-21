<?php

namespace Modules\CMS\Sliders\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\CMS\Sliders\Models\Slider;

interface SliderRepositoryInterface
{
    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator;

    public function activeSorted(): Collection;

    public function findById(int $id): ?Slider;
}


