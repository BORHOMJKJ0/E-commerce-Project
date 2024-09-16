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

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all categories",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CategoryResource")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json(CategoryResource::collection($categories), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a category",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="fruits")
     *         )
     *     ),
     *    @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->all());
        return response()->json([
            'message' => 'Category created successfully!',
            'category' => CategoryResource::make($category),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get a category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return response()->json(CategoryResource::make($category), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/order/{column}/{direction}",
     *     summary="Order categories by a specific column",
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", enum={"name", "created_at", "updated_at"})
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CategoryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid column or direction"
     *     )
     * )
     */
    public function orderBy($column, $direction): JsonResponse
    {
        $validColumns = ['name', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (!in_array($column, $validColumns) || !in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $categories = $this->categoryService->getCategoriesOrderedBy($column, $direction);
        return response()->json(CategoryResource::collection($categories), 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="Updated Category Name")
     *     ),
     *      @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->all());
        return response()->json([
            'message' => 'Category updated successfully!',
            'category' => CategoryResource::make($category),
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        return response()->json(['message' => 'Category deleted successfully!'], 200);
    }
}