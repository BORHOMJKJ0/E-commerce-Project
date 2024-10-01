<?php

namespace App\Http\Controllers\Warehouse;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\Exceptions\HttpResponseException;
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

    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $warehouses = $this->warehouseService->getAllWarehouses($page, $items);
        $hasMorePages = $warehouses->hasMorePages();

        $data = [
            'Warehouses' => WarehouseResource::collection($warehouses),
            'hasMorePages' => $hasMorePages,
        ];
        return ResponseHelper::jsonRespones($data);
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $warehouse = $this->warehouseService->createWarehouse($request->all());

            $data = ['warehouse' => WarehouseResource::make($warehouse)];
            return ResponseHelper::jsonRespones($data, 'Warehouse created successfully!', 201);
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        try {

            $warehouse = $this->warehouseService->getWarehouseById($warehouse);

            $data = ['warehouse' => WarehouseResource::make($warehouse)];
            return ResponseHelper::jsonRespones($data, 'Warehouse performed successfully!');
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['expiry_date', 'created_at', 'updated_at', 'payment_date', 'settlement_date', 'pure_price'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $warehouses = $this->warehouseService->getWarehousesOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $warehouses->hasMorePages();

        $data = [
            'Warehouses' => WarehouseResource::collection($warehouses),
            'hasMorePages' => $hasMorePages,
        ];
        return ResponseHelper::jsonRespones($data, 'Warehouses ordered successfully');
    }

    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        try {

            $warehouse = $this->warehouseService->updateWarehouse($warehouse, $request->all());

            $data = ['warehouse' => WarehouseResource::make($warehouse)];
            return ResponseHelper::jsonRespones($data, 'Warehouse updated successfully!');
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        try {

            $this->warehouseService->deleteWarehouse($warehouse);
            return ResponseHelper::jsonRespones([], 'Warehouse deleted successfully!');
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }
}
