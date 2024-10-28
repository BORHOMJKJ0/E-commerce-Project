<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SearchProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Traits\AuthTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductService
{
    use AuthTrait;

    protected ProductRepository $productRepository;

    private OfferService $offerService;

    private CategoryService $categoryService;

    private WarehouseService $warehouseService;

    private ImageService $imageService;

    //    protected $fcmService;

    public function __construct(ProductRepository $productRepository, CategoryService $categoryService,
        WarehouseService $warehouseService, OfferService $offerService,
        ImageService $imageService,
        //                                FcmService $fcmService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryService = $categoryService;
        $this->warehouseService = $warehouseService;
        $this->offerService = $offerService;
        $this->imageService = $imageService;
        // $this->fcmService = $fcmService;
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
     *         response=400,
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
     *         response=400,
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
     *     description="Product ID you want to show it",
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
     *                 required={"name", "price", "description", "category_id"},
     *
     *                 @OA\Property(property="name", type="string", example="Perform", description="Product Name"),
     *                 @OA\Property(property="price", type="number", format="float", example=250.75, description="Product Price"),
     *                 @OA\Property(property="description", type="string", example="This is a new Perform and it's cool. Try it", description="Product Description"),
     *                 @OA\Property(property="category_id", type="integer", example=1, description="Category ID the product belongs to"),
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
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 description="List of image URLs",
     *
     *                 @OA\Items(
     *                     type="string",
     *                     example="https://example.com/images/smartphone-xyz.jpg"
     *                 )
     *             ),
     *
     *             @OA\Property(property="price", type="number", format="float", example=499.99),
     *             @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design."),
     *             @OA\Property(property="current_price", type="number", format="float", example=449.99),
     *             @OA\Property(property="user", type="string", example="Hasan Zaeter"),
     *             @OA\Property(property="total_amount", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="null"),
     *             @OA\Property(property="category", type="string", example="Smartphones")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to add this product.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     )
     * )
     */
    public function createProduct(array $data): Product
    {
        $data['user_id'] = auth()->id();
        $this->validateProductData($data);
        $product = $this->productRepository->create($data);

        // $this->fcmService->notifyUsers($product);
        return $product;
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
     *         description="Column you want to order the products by it",
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
     *     description="Dircetion of ordering",
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
     *         response=400,
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
     *         description="Column you want to order the products by it",
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
     *     description="Dircetion of ordering",
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
     *         response=400,
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

    /**
     * @OA\Get(
     *     path="api/products/search",
     *     summary="Search products by filters",
     *     description="Retrieve a paginated list of products filtered by various criteria.",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="Products",
     *                     type="array",
     *
     *                    @OA\Items(ref="#/components/schemas/ProductResource")
     *                 ),
     *
     *                 @OA\Property(property="hasMorePages", type="boolean", example=true)
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="No products found for the given filters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No products found for the given filters"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
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
     *         description="Product ID you want to update",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="Product name",
     *
     *         @OA\Schema(type="string", example="Perform")
     *     ),
     *
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=false,
     *         description="Product price",
     *
     *         @OA\Schema(type="number", format="float", example=50.75,)
     *     ),
     *
     *      @OA\Parameter(
     *         name="decription",
     *         in="query",
     *         required=false,
     *         description="Product Drecription",
     *
     *         @OA\Schema(type="string", example="This is a new Perform and it's cool. Try it")
     *     ),
     *
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="Category ID the product belongs to",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=42),
     *             @OA\Property(property="name", type="string", example="Iphone 14"),
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 description="List of image URLs",
     *
     *                 @OA\Items(
     *                     type="string",
     *                     example="https://example.com/images/smartphone-xyz.jpg"
     *                 )
     *             ),
     *
     *             @OA\Property(property="price", type="number", format="float", example=500),
     *             @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design."),
     *             @OA\Property(property="current_price", type="number", format="float", example=449.99),
     *             @OA\Property(property="user", type="string", example="Hasan Zaeter"),
     *             @OA\Property(property="total_amount", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="null"),
     *             @OA\Property(property="category", type="string", example="Smartphones")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden error",
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
     *     description="Product ID you want to delete it",
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

    public function create_product_with_details(Request $request): JsonResponse
    {
        $request->validate([
            'category_name' => 'required',
            'product_name' => 'required',
            'product_description' => 'required',
            'product_price' => 'required',
            'images' => 'required|array',
            'warehouse' => 'required|array',
            'warehouse.*.offers' => 'required|array',
        ]);
        DB::transaction(function () use ($request) {
            $category_id = $this->categoryService->CreateCategoryOrFind($request->input('category_name'));

            $data = [
                'name' => $request->input('product_name'),
                'description' => $request->input('product_description'),
                'price' => $request->input('product_price'),
                'category_id' => $category_id,
            ];

            $product = $this->createProduct($data);

            $imagesData = $request->input('images');

            foreach ($imagesData as $image) {

                $data = [
                    'image' => $image['image'],
                    'main' => $image['main'],
                    'product_id' => $product->id,
                ];

                $result = $this->imageService->createImage($data);
                if ($result instanceof JsonResponse) {
                    return $result;
                }
            }

            $warehousesData = $request->input('warehouse');

            foreach ($warehousesData as $warehouseData) {
                $data = [
                    'amount' => $warehouseData['amount'],
                    'expiry_date' => $warehouseData['expiry_date'],
                    'product_id' => $product->id,
                ];

                $warehouse = $this->warehouseService->createWarehouse($data);

                if ($warehouse instanceof JsonResponse) {
                    return $warehouse; // Abort if warehouse creation fails
                }

                foreach ($warehouseData['offers'] as $offer) {
                    $offerData = [
                        'discount_percentage' => $offer['discount_percentage'],
                        'start_date' => $offer['start_date'],
                        'end_date' => $offer['end_date'],
                        'warehouse_id' => $warehouse->id,
                    ];

                    $offerResult = $this->offerService->createOffer($offerData);

                    if ($offerResult instanceof JsonResponse) {
                        return $offerResult; // Abort if offer creation fails
                    }
                }
            }

            return ResponseHelper::jsonResponse([], 'Products and its details added successfully!', 201);
        });

        return ResponseHelper::jsonResponse([], 'Products and its details added successfully!', 201);
    }

    protected function validateProductData(array $data, $rule = 'required'): void
    {
        $validator = Validator::make($data, [
            'name' => "$rule|string|max:255|unique:products,name",
            'price' => "$rule|numeric|min:0",
            'description' => "$rule|string|max:1000",
            'category_id' => "$rule|exists:categories,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
