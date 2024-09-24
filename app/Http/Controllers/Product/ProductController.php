<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\UnauthorizedActionException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->middleware('auth:api');
        $this->productService = $productService;
    }

    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $this->productService->getAllProducts($page, $items);
        $hasMorePages = $products->hasMorePages();

        return response()->json([
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function MyProducts(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $this->productService->getMyProducts($page, $items);
        $hasMorePages = $products->hasMorePages();

        return response()->json([
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->all());

            return response()->json([
                'message' => 'Product created successfully!',
                'product' => ProductResource::make($product),
            ], 201);
        } catch (UnauthorizedActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function show(Product $product): JsonResponse
    {
        $product = $this->productService->getProductById($product);

        return response()->json(ProductResource::make($product), 200);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['name', 'price', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $this->productService->getProductsOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $products->hasMorePages();

        return response()->json([
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function MyProductsOrderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['name', 'price', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $this->productService->getMyProductsOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $products->hasMorePages();

        return response()->json([
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($product, $request->all());

            return response()->json([
                'message' => 'Product updated successfully!',
                'product' => ProductResource::make($product),
            ], 200);
        } catch (UnauthorizedActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $this->productService->deleteProduct($product);

            return response()->json(['message' => 'Product deleted successfully!'], 200);
        } catch (UnauthorizedActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
