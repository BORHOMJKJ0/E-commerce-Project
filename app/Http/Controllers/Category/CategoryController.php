<?php

namespace App\Http\Controllers\Category;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Exceptions\HttpResponseException;
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
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $categories = $this->categoryService->getAllCategories($page, $items);
        $hasMorePages = $categories->hasMorePages();

        return response()->json([
            'categories' => CategoryResource::collection($categories),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);
    }

    public function MyCategories(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $categories = $this->categoryService->getMyCategories($page, $items);
        $hasMorePages = $categories->hasMorePages();

        return response()->json([
            'categories' => CategoryResource::collection($categories),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->all());

        return response()->json([
            'message' => 'Category created successfully!',
            'category' => CategoryResource::make($category),
            'successful' => true,
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($category);

        return response()->json([
            'message' => 'Category performed successfully!',
            'category' => CategoryResource::make($category),
            'successful' => true,
        ], 200);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['name', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $categories = $this->categoryService->getCategoriesOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $categories->hasMorePages();

        return response()->json([
            'categories' => CategoryResource::collection($categories),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);

    }

    public function MyCategoriesOrderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['name', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $categories = $this->categoryService->getMyCategoriesOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $categories->hasMorePages();

        return response()->json([
            'categories' => CategoryResource::collection($categories),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);

    }

    public function update(Request $request, Category $category): JsonResponse
    {
        try {

            $category = $this->categoryService->updateCategory($category, $request->all());

            return ResponseHelper::jsonResponse(CategoryResource::make($category), 'Category updated successfully!', 200, true);
        } catch (HttpResponseException $e) {
            return response()->json($e->getResponse()->getData(), 403);
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        try {

            $this->categoryService->deleteCategory($category);

            return response()->json(['message' => 'Category deleted successfully!', 'successful' => true], 200);
        } catch (HttpResponseException $e) {
            return response()->json($e->getResponse()->getData(), 403);
        }

    }
}
