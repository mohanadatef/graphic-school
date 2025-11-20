<?php

namespace Modules\LMS\Categories\Services;

use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Categories\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function list(): Collection
    {
        return $this->categoryRepository->listOrdered();
    }

    public function create(array $data): Category
    {
        return $this->categoryRepository->create([
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}

