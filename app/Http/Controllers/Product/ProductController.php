<?php

namespace App\Http\Controllers\Product;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Exceptions\HttpResponseException;
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

        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonRespones($data, 'Products retrieved successfully');
    }

    public function MyProducts(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $this->productService->getMyProducts($page, $items);
        $hasMorePages = $products->hasMorePages();

        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonRespones($data, 'Products retrieved successfully');
    }
    public function store(Request $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->all());

            $data = ['product' => ProductResource::make($product)];
            return ResponseHelper::jsonRespones($data, 'Product created successfully!', 201);
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }

    public function show(Product $product): JsonResponse
    {
        $product = $this->productService->getProductById($product);

        $data = ['product' => ProductResource::make($product)];
        return ResponseHelper::jsonRespones($data, 'Product retrieved successfully!');
    }

    public function ProductOrderBy($column, $direction, Request $request, bool $isMyProduct = false)
    {
        $validColumns = ['name', 'price', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonRespones([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $isMyProduct
            ? $this->productService->getMyProductsOrderedBy($column, $direction, $page, $items)
            : $this->productService->getProductsOrderedBy($column, $direction, $page, $items);

        $hasMorePages = $products->hasMorePages();

        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];
        return ResponseHelper::jsonRespones($data, 'Products ordered successfully!');
    }
    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->ProductOrderBy($column, $direction, $request);
    }

    public function MyProductsOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->ProductOrderBy($column, $direction, $request, true);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($product, $request->all());

            $data = ['product' => ProductResource::make($product)];
            return ResponseHelper::jsonRespones($data, 'Product updated successfully!');
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $this->productService->deleteProduct($product);

            return ResponseHelper::jsonRespones([], 'Product deleted successfully!');
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }
}
