<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
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

    public function show($id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);

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

    public function update(Request $request, $id): JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->all());

        return response()->json([
            'message' => 'Category updated successfully!',
            'category' => CategoryResource::make($category),
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);

        return response()->json(['message' => 'Category deleted successfully!'], 200);
    }
}
