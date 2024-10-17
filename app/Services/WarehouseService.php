<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\WarehouseResource;
use App\Models\Product;
use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use App\Traits\AuthTrait;
use App\Traits\ValidationTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WarehouseService
{
    use AuthTrait,ValidationTrait;

    protected $warehouseRepository;

    public function __construct(WarehouseRepository $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses",
     *     summary="Get my warehouses",
     *     tags={"Warehouse"},
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
     *             @OA\Items(ref="#/components/schemas/WarehouseResource")
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
    public function getAllWarehouses(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $warehouses = $this->warehouseRepository->getAll($items, $page);
        $hasMorePages = $warehouses->hasMorePages();

        $data = [
            'Warehouses' => WarehouseResource::collection($warehouses),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Warehouses retrieved successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses/{id}",
     *     summary="Get a warehouse by ID",
     *     tags={"Warehouse"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="your Warehouse ID you want to show it",
     *
     *        @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseResource")
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to view this warehouse.")
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
    public function getWarehouseById(Warehouse $warehouse)
    {
        try {
            $product = $warehouse->product;
            $this->checkOwnership($product, 'Warehouse', 'perform');
            $data = ['warehouse' => WarehouseResource::make($warehouse)];
            $response = ResponseHelper::jsonResponse($data, 'Warehouse retrieved successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Post(
     *     path="/api/warehouses",
     *     summary="Create a warehouse",
     *     tags={"Warehouse"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *      @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *             required={"amount", "expiry_date", "product_id"},
     *
     *             @OA\Property(property="amount", type="number", example=100,description="Warehouse Amount"),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01",description="Warehouse exoiry date"),
     *             @OA\Property(property="product_id", type="integer", example=1,description="Product ID that you want to add this warehouse to it")
     *             )
     *         )
     *     ),
     *
     *      @OA\Header(
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
     *         response=201,
     *         description="Warehouse created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseResource")
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to create this warehouse .")
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
     * )
     */
    public function createWarehouse(array $data)
    {
        try {
            $product = Product::find($data['product_id']);
            $this->validateWarehouseData($data);
            $this->checkOwnership($product, 'Warehouse', 'create');
            $this->checkDate($data, 'expiry_date', 'future');
            $warehouse = $this->warehouseRepository->create($data);
            $data = ['warehouse' => WarehouseResource::make($warehouse)];
            $response = ResponseHelper::jsonResponse($data, 'Warehouse created successfully!', 201);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses/order/{column}/{direction}",
     *     summary="Order My warehouses by a specific column",
     *     tags={"Warehouse"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *     description="Column you want to order the warehouses by it",
     *
     *         @OA\Schema(type="string", enum={"expiry_date", "created_at", "updated_at", "payment_date", "settlement_date", "pure_price"})
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
     *    @OA\Parameter(
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/WarehouseResource")
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
    public function getWarehousesOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['expiry_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $warehouses = $this->warehouseRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $warehouses->hasMorePages();

        $data = [
            'Warehouses' => WarehouseResource::collection($warehouses),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Warehouses ordered successfully');

    }

    /**
     * @OA\Put(
     *     path="/api/warehouses/{id}",
     *     summary="Update a warehouse",
     *     tags={"Warehouse"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="your Warehouse ID that you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=false,
     *     description="THe amount of this warehouse",
     *
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=false,
     *     description="Product ID of this warehouse",
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
     *         description="Warehouse updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="amount", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(
     *                 property="product",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Iphone 15"),
     *                 @OA\Property(property="price", type="number", format="float", example="499.99"),
     *                 @OA\Property(property="category", type="string", example="Smartphone"),
     *                 @OA\Property(property="user", type="string", example="Hasan Zaeter")
     *             )
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
     *  @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this warehouse .")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Warehouse not found")
     *         )
     *     )
     * )
     */
    public function updateWarehouse(Warehouse $warehouse, array $data)
    {
        if (isset($data['expiry_date'])) {
            throw ValidationException::withMessages([
                'expiry_date' => 'You cannot update the expiry date once it has been set.',
            ]);
        }
        try {
            $this->validateWarehouseData($data, $warehouse, 'sometimes', 0);
            $product = $warehouse->product;
            $this->checkOwnership($product, 'Warehouse', 'update');

            $warehouse = $this->warehouseRepository->update($warehouse, $data);

            $data = ['warehouse' => WarehouseResource::make($warehouse)];
            $response = ResponseHelper::jsonResponse($data, 'Warehouse updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/warehouses/{id}",
     *     summary="Delete a warehouse",
     *     tags={"Warehouse"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="your Warehouse ID you want to delete it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Warehouse deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Warehouse deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this warehouse .")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Warehouse not found")
     *         )
     *     )
     * )
     */
    public function deleteWarehouse(Warehouse $warehouse)
    {

        try {
            $product = $warehouse->product;
            $this->checkOwnership($product, 'Warehouse', 'delete');
            $this->warehouseRepository->delete($warehouse);
            $response = ResponseHelper::jsonResponse([], 'Warehouse deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validateWarehouseData(array $data, $warehouse = null, $rule = 'required', $limit = 1)
    {
        $validator = Validator::make($data, [
            'amount' => "$rule|numeric|min:$limit",
            'expiry_date' => "$rule|date|after_or_equal:payment_date",
            'product_id' => "$rule|exists:products,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
