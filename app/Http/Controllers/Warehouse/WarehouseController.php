<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
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

    public function index(Request $request): JsonResponse
    {
        return $this->warehouseService->getAllWarehouses($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->warehouseService->createWarehouse($request->all());
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->warehouseService->getWarehouseById($warehouse);
    }

    public function getWarehousesHaveOffers(Product $product, Request $request)
    {
        return $this->warehouseService->getWarehousesHaveOffers($product, $request);
    }
    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $warehouses = $this->warehouseService->getWarehousesOrderedBy($column, $direction, $request);
    }

    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        return $this->warehouseService->updateWarehouse($warehouse, $request->all());
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        return $this->warehouseService->deleteWarehouse($warehouse);
    }
}
