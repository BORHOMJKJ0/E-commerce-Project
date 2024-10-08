<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SearchProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Traits\AuthTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductService
{
    use AuthTrait;

    protected $productRepository;
    protected $fcmService;

    public function __construct(ProductRepository $productRepository, FcmService $fcmService)
    {
        $this->productRepository = $productRepository;
        $this->fcmService = $fcmService;
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function getAllProducts(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $products = $this->productRepository->getAll($items, $page);

        $hasMorePages = $products->hasMorePages();

        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Products retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/products/my",
     *     summary="Get My products",
     *     tags={"Products"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function getMyProducts(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $products = $this->productRepository->getMy($items, $page);
        $hasMorePages = $products->hasMorePages();

        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Products retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a single product by ID",
     *     tags={"Products"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function getProductById(Product $product)
    {
        $data = ['product' => ProductResource::make($product)];

        return ResponseHelper::jsonResponse($data, 'Product retrieved successfully!');
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a product",
     *     tags={"Products"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "image", "price", "description", "category_id"},
     *
     *                 @OA\Property(property="name", type="string", example="Perform"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="price", type="number", format="float", example=250.75),
     *                 @OA\Property(property="description", type="string", example="This is a new Perform and it's cool Try it"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *             )
     *         )
     *     ),
     *
     *     @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="multipart/form-data")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=42),
     *             @OA\Property(property="name", type="string", example="Iphone 15"),
     *             @OA\Property(property="image", type="string", example="https://example.com/images/smartphone-xyz.jpg"),
     *             @OA\Property(property="price", type="number", format="float", example=499.99),
     *             @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design."),
     *             @OA\Property(property="current_price", type="number", format="float", example=449.99),
     *             @OA\Property(property="user", type="string", example="Hasan Zaeter"),
     *             @OA\Property(
     *                 property="offers",
     *                 type="array",
     *                 example={},
     *
     *                 @OA\Items()
     *             ),
     *
     *             @OA\Property(property="total_amount", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="null"),
     *             @OA\Property(property="category", type="string", example="Smartphones")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to add this product.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     )
     * )
     */
    public function createProduct(array $data)
    {
        $data['user_id'] = auth()->id();
        $this->validateProductData($data);
        $product = $this->productRepository->create($data);

        $this->fcmService->notifyUsers($product);

        $data = [
            'Product' => ProductResource::make($product),
        ];

        return ResponseHelper::jsonResponse($data, 'Product created successfully!', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/order/{column}/{direction}",
     *     summary="Order products by a specific column",
     *     tags={"Products"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"name", "price", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page ",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction or parameters")
     *         )
     *     )
     * )
     */
    public function getProductsOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['name', 'price', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $this->productRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $products->hasMorePages();
        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Products ordered successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/products/my/order/{column}/{direction}",
     *     summary="Order My products by a specific column",
     *     tags={"Products"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"name", "price", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page ",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction or parameters")
     *         )
     *     )
     * )
     */
    public function getMyProductsOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['name', 'price', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $products = $this->productRepository->orderMyBy($column, $direction, $page, $items);
        $hasMorePages = $products->hasMorePages();
        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Products ordered successfully!');
    }

    public function searchByFilters(SearchProductRequest $request)
    {

        $page = $request->query('page', 1);
        $items = $request->query('items', 10);

        $products = $this->productRepository->getProductsByFilters($request, $items, $page);

        if (! $products) {
            return ResponseHelper::jsonResponse([], 'No products found for the given filters.');
        }

        $hasMorePages = $products->hasMorePages();
        $data = [
            'Products' => ProductResource::collection($products),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Products retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="Perform")
     *     ),
     *
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="number", format="float", example=50.75)
     *     ),
     *
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="This is a new Perform and it's cool Try it")
     *     ),
     *
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Iphone 14"),
     *             @OA\Property(property="image", type="string", example="https://example.com/images/smartphone-xyz.jpg"),
     *             @OA\Property(property="price", type="number", format="float", example=499.99),
     *             @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design."),
     *             @OA\Property(property="current_price", type="number", format="float", example=449.99),
     *             @OA\Property(property="user", type="string", example="Hasan Zaeter"),
     *             @OA\Property(
     *                 property="offers",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="discount", type="string", example="10.00"),
     *                     @OA\Property(property="starting_at", type="string", format="date-time", example="2024-09-22 12:00"),
     *                     @OA\Property(property="ending_at", type="string", format="date-time", example="2025-01-15 23:59")
     *                 )
     *             ),
     *             @OA\Property(property="total_amount", type="integer", example=250),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2025-01-15"),
     *             @OA\Property(property="category", type="string", example="Smartphones")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *
     *      @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to update this product.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function updateProduct(Product $product, array $data)
    {
        try {
            $this->validateProductData($data, 'sometimes');
            $this->checkOwnership($product, 'Product', 'update');
            $product = $this->productRepository->update($product, $data);
            $data = [
                'Product' => ProductResource::make($product),
            ];

            $response = ResponseHelper::jsonResponse($data, 'Product updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this product.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function deleteProduct(Product $product)
    {
        try {
            $this->checkOwnership($product, 'Product', 'delete');
            $this->checkProduct($product, 'Products', 'delete');
            $this->productRepository->delete($product);
            $response = ResponseHelper::jsonResponse([], 'Product deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validateProductData(array $data, $rule = 'required')
    {
        $validator = Validator::make($data, [
            'name' => "$rule|string|max:255|unique:products,name",
            'image' => "$rule|image|max:5120",
            'price' => "$rule|numeric|min:0",
            'description' => "$rule|string|max:1000",
            'category_id' => "$rule|exists:categories,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
