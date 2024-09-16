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

    public function index(): JsonResponse
    {
        $warehouses = $this->warehouseService->getAllWarehouses();

        return response()->json(WarehouseResource::collection($warehouses), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $warehouse = $this->warehouseService->createWarehouse($request->all());

        return response()->json([
            'message' => 'Warehouse created successfully!',
            'warehouse' => WarehouseResource::make($warehouse),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $warehouse = $this->warehouseService->getWarehouseById($id);

        return response()->json(WarehouseResource::make($warehouse), 200);
    }

    public function orderBy($column, $direction): JsonResponse
    {
        $validColumns = ['expiry_date', 'created_at', 'updated_at', 'payment_date', 'settlement_date', 'pure_price'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $warehouses = $this->warehouseService->getWarehousesOrderedBy($column, $direction);

        return response()->json(WarehouseResource::collection($warehouses), 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $warehouse = $this->warehouseService->updateWarehouse($id, $request->all());

        return response()->json([
            'message' => 'Warehouse updated successfully!',
            'warehouse' => WarehouseResource::make($warehouse),
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $this->warehouseService->deleteWarehouse($id);

        return response()->json(['message' => 'Warehouse deleted successfully!'], 200);
    }
}
