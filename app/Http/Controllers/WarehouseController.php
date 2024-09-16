<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarehouseResource;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class WarehouseController extends Controller
{
    const VALID_COLUMNS = ['expiry_date', 'created_at', 'updated_at', 'payment_date', 'settlement_date', 'pure_price'];
    const VALID_DIRECTIONS = ['asc', 'desc'];

    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses",
     *     summary="Get all warehouses",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/WarehouseResource")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $warehouses = $this->warehouseService->getAllWarehouses();
        return response()->json(WarehouseResource::collection($warehouses), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/warehouses",
     *     summary="Create a warehouse",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"amount", "pure_price", "payment_date", "settlement_date", "expiry_date", "product_id"},
     *             @OA\Property(property="amount", type="number", example=100),
     *             @OA\Property(property="pure_price", type="number", example=50.75),
     *             @OA\Property(property="payment_date", type="string", format="date", example="2024-09-01"),
     *             @OA\Property(property="settlement_date", type="string", format="date", example="2024-09-15"),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01"),
     *             @OA\Property(property="product_id", type="integer", example=1)
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
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseResource")
     *     ),
     *   @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *   )
     */
    public function store(Request $request): JsonResponse
    {
        $warehouse = $this->warehouseService->createWarehouse($request->all());
        return response()->json([
            'message' => 'Warehouse created successfully!',
            'warehouse' => WarehouseResource::make($warehouse),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses/{id}",
     *     summary="Get a warehouse by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $warehouse = $this->warehouseService->getWarehouseById($id);
        return response()->json(WarehouseResource::make($warehouse), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses/order/{column}/{direction}",
     *     summary="Order warehouses by a specific column",
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", enum={"expiry_date", "created_at", "updated_at", "payment_date", "settlement_date", "pure_price"})
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
     *             @OA\Items(ref="#/components/schemas/WarehouseResource")
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
        $validColumns = ['expiry_date', 'created_at', 'updated_at', 'payment_date', 'settlement_date', 'pure_price'];
        $validDirections = ['asc', 'desc'];

        if (!in_array($column, $validColumns) || !in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $warehouses = $this->warehouseService->getWarehousesOrderedBy($column, $direction);
        return response()->json(WarehouseResource::collection($warehouses), 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/warehouses/{id}",
     *     summary="Update a warehouse",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="pure_price",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="payment_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="settlement_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="expiry_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
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
     *         description="Warehouse updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $warehouse = $this->warehouseService->updateWarehouse($id, $request->all());
        return response()->json([
            'message' => 'Warehouse updated successfully!',
            'warehouse' => WarehouseResource::make($warehouse),
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/warehouses/{id}",
     *     summary="Delete a warehouse",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Warehouse deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $this->warehouseService->deleteWarehouse($id);
        return response()->json(['message' => 'Warehouse deleted successfully!'], 200);
    }
}
