<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
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

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();

        return response()->json(CategoryResource::collection($categories), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->all());

        return response()->json([
            'message' => 'Category created successfully!',
            'category' => CategoryResource::make($category),
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($category);

        return response()->json(CategoryResource::make($category), 200);
    }

    public function orderBy($column, $direction): JsonResponse
    {
        $validColumns = ['name', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $categories = $this->categoryService->getCategoriesOrderedBy($column, $direction);

        return response()->json(CategoryResource::collection($categories), 200);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->updateCategory($category, $request->all());

        return response()->json([
            'message' => 'Category updated successfully!',
            'category' => CategoryResource::make($category),
        ], 200);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);

        return response()->json(['message' => 'Category deleted successfully!'], 200);
    }
}