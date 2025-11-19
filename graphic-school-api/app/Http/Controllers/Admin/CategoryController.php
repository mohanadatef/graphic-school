<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;

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
