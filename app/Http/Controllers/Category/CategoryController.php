<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:api');
        $this->categoryService = $categoryService;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->categoryService->getAllCategories($request);
    }

    public function MyCategories(Request $request): JsonResponse
    {
        return $this->categoryService->getMyCategories($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->categoryService->createCategory($request->all());
    }

    public function show(Category $category): JsonResponse
    {
        return $this->categoryService->getCategoryById($category);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->categoryService->getCategoriesOrderedBy($column, $direction, $request);
    }

    public function MyCategoriesOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->categoryService->getMyCategoriesOrderedBy($column, $direction, $request);
    }

    public function update(Request $request, Category $category): JsonResponse
    {

        return $this->categoryService->updateCategory($category, $request->all());
    }

    public function destroy(Category $category): JsonResponse
    {
        return $this->categoryService->deleteCategory($category);
    }
}
