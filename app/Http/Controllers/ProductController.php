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
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();

        return response()->json(ProductResource::collection($products), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->all());

        return response()->json([
            'message' => 'Product created successfully!',
            'product' => ProductResource::make($product),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        return response()->json(ProductResource::make($product), 200);
    }

    public function orderBy($column, $direction): JsonResponse
    {
        $validColumns = ['name', 'price', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $products = $this->productService->getProductsOrderedBy($column, $direction);

        return response()->json(ProductResource::collection($products), 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->all());

        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => ProductResource::make($product),
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $this->productService->deleteProduct($id);

        return response()->json(['message' => 'Product deleted successfully!'], 200);
    }
}
