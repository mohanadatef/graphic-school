<?php

namespace Modules\LMS\Categories\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use App\Contracts\Repositories\CategoryRepositoryInterface as SharedCategoryRepositoryInterface;
use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Categories\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface, SharedCategoryRepositoryInterface
{
    /**
     * Make model instance
     * Follows Liskov Substitution Principle - returns Model, not concrete Category
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Category();
    }

    public function listOrdered(): Collection
    {
        return $this->query()->orderBy('name')->get();
    }

    public function activeOrdered(): Collection
    {
        return $this->query()->where('is_active', true)->orderBy('name')->get();
    }
}

