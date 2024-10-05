<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
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
        return $this->productService->getAllProducts($request);
    }

    public function MyProducts(Request $request): JsonResponse
    {
        return $this->productService->getMyProducts($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->productService->createProduct($request->all());
    }

    public function show(Product $product): JsonResponse
    {
        return $this->productService->getProductById($product);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->productService->getProductsOrderedBy($column, $direction, $request);
    }

    public function MyProductsOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->productService->getMyProductsOrderedBy($column, $direction, $request);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        return $this->productService->updateProduct($product, $request->all());
    }

    public function destroy(Product $product): JsonResponse
    {
        return $this->productService->deleteProduct($product);
    }
}
