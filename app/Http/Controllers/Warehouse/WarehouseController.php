<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->middleware('auth:api');
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

    public function show(Warehouse $warehouse): JsonResponse
    {
        $warehouse = $this->warehouseService->getWarehouseById($warehouse);

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

    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $warehouse = $this->warehouseService->updateWarehouse($warehouse, $request->all());

        return response()->json([
            'message' => 'Warehouse updated successfully!',
            'warehouse' => WarehouseResource::make($warehouse),
        ], 200);
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $this->warehouseService->deleteWarehouse($warehouse);

        return response()->json(['message' => 'Warehouse deleted successfully!'], 200);
    }
}
