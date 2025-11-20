<?php

namespace Modules\LMS\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\LMS\Categories\Http\Requests\StoreCategoryRequest;
use Modules\LMS\Categories\Http\Requests\UpdateCategoryRequest;
use Modules\LMS\Categories\Http\Resources\CategoryResource;
use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Categories\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    public function index()
    {
        return CategoryResource::collection($this->categoryService->list());
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        $category = $this->categoryService->create($data);

        return CategoryResource::make($category)
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        $category = $this->categoryService->update($category, $data);

        return CategoryResource::make($category);
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);

        return response()->json(['message' => 'Deleted']);
    }
}

