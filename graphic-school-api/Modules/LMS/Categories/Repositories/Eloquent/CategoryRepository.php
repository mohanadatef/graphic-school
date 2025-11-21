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
        $locale = app()->getLocale();
        return $this->query()
            ->with(['translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->orderBy('id')
            ->get()
            ->map(function ($category) use ($locale) {
                // Set name attribute based on locale
                $translation = $category->translations->first();
                if ($translation) {
                    $category->setAttribute('name', $translation->name);
                    $category->setAttribute('localized_name', $translation->name);
                }
                return $category;
            });
    }

    public function activeOrdered(): Collection
    {
        $locale = app()->getLocale();
        return $this->query()
            ->where('is_active', true)
            ->with(['translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->orderBy('id')
            ->get()
            ->map(function ($category) use ($locale) {
                // Set name attribute based on locale
                $translation = $category->translations->first();
                if ($translation) {
                    $category->setAttribute('name', $translation->name);
                    $category->setAttribute('localized_name', $translation->name);
                }
                return $category;
            });
    }
}

