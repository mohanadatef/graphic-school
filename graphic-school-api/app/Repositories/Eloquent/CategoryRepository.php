<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
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


