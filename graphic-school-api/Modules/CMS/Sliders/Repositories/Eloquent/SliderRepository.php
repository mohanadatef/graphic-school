<?php

namespace Modules\CMS\Sliders\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\CMS\Sliders\Models\Slider;
use Modules\CMS\Sliders\Repositories\Interfaces\SliderRepositoryInterface;
use App\Support\Repositories\BaseRepository;

class SliderRepository extends BaseRepository implements SliderRepositoryInterface
{
    /**
     * Make model instance
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Slider();
    }

    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->orderBy('sort_order');

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate($perPage);
    }

    public function activeSorted(): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findById(int $id): ?Slider
    {
        return $this->query()->find($id);
    }
}


