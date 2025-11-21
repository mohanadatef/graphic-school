<?php

namespace Modules\LMS\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
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
        $categories = $this->categoryService->list();
        return ApiResponse::collection(
            CategoryResource::collection($categories)->resolve(request()),
            'Categories retrieved successfully'
        );
    }

    public function show(Category $category)
    {
        return ApiResponse::success(
            CategoryResource::make($category->load('translations'))->resolve(request()),
            'Category retrieved successfully'
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        $category = $this->categoryService->create($data);

        return ApiResponse::created(
            CategoryResource::make($category)->resolve(request()),
            'Category created successfully'
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        $category = $this->categoryService->update($category, $data);

        return ApiResponse::success(
            CategoryResource::make($category)->resolve(request()),
            'Category updated successfully'
        );
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);

        return ApiResponse::success(
            null,
            'Category deleted successfully'
        );
    }
}

