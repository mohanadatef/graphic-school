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
        $category = $this->categoryRepository->create([
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Save translations
        if (isset($data['translations']) && is_array($data['translations'])) {
            foreach ($data['translations'] as $locale => $name) {
                if (!empty($name)) {
                    $category->translations()->create([
                        'locale' => $locale,
                        'name' => $name,
                    ]);
                }
            }
        }

        return $category->load('translations');
    }

    public function update(Category $category, array $data): Category
    {
        $this->categoryRepository->update($category, [
            'is_active' => $data['is_active'] ?? $category->is_active,
        ]);

        // Update translations
        if (isset($data['translations']) && is_array($data['translations'])) {
            foreach ($data['translations'] as $locale => $name) {
                if (!empty($name)) {
                    $category->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $name]
                    );
                } else {
                    $category->translations()->where('locale', $locale)->delete();
                }
            }
        }

        return $category->load('translations');
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}

