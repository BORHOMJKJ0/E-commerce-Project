<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    const VALID_COLUMNS = ['name', 'price', 'created_at', 'updated_at'];
    const VALID_DIRECTIONS = ['asc', 'desc'];

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return response()->json(ProductResource::collection($products), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "image", "price", "category_id", "user_id"},
     *             @OA\Property(property="name", type="string", example="Sample Product"),
     *             @OA\Property(property="image", type="string", example="image_url.jpg"),
     *             @OA\Property(property="price", type="number", format="float", example=50.75),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
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
     *         response=201,
     *         description="Warehouse created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *   @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *   )
     */

    public function store(Request $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->all());
        return response()->json([
            'message' => 'Product created successfully!',
            'product' => ProductResource::make($product),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a single product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        return response()->json(ProductResource::make($product), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/order/{column}/{direction}",
     *     summary="Order products by a specific column",
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", enum={"name", "price", "created_at", "updated_at"})
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
     *             @OA\Items(ref="#/components/schemas/ProductResource")
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
        $validColumns = ['name', 'price', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (!in_array($column, $validColumns) || !in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $products = $this->productService->getProductsOrderedBy($column, $direction);
        return response()->json(ProductResource::collection($products), 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}",
     *     summary="Update a product",
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
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
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
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->all());
        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => ProductResource::make($product),
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully!'], 200);
    }
}
