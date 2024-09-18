<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WarehouseService
{
    protected $warehouseRepository;

    public function __construct(WarehouseRepository $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses",
     *     summary="Get all warehouses",
     *     tags={"Warehouse"},
     *     security={{"bearerAuth": {} }},
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
     *     )
     * )
     */
    public function getAllWarehouses()
    {
        return $this->warehouseRepository->getAll();
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
        return $warehouse;
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
     *             required={"amount", "pure_price", "payment_date", "expiry_date", "product_id"},
     *
     *             @OA\Property(property="amount", type="number", example=100),
     *             @OA\Property(property="pure_price", type="number", example=50.75),
     *             @OA\Property(property="payment_date", type="string", format="date", example="2024-09-01"),
     *             @OA\Property(property="settlement_date", type="string", format="date", example="2024-09-15"),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01"),
     *             @OA\Property(property="product_id", type="integer", example=1)
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
     *     @OA\Response(
     *         response=422,
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
        $data['settlement_date'] = null;

        $this->validateWarehouseData($data);

        return $this->warehouseRepository->create($data);
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses/order/{column}/{direction}",
     *     summary="Order warehouses by a specific column",
     *     tags={"Warehouse"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"expiry_date", "created_at", "updated_at", "payment_date", "settlement_date", "pure_price"})
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
     *             @OA\Items(ref="#/components/schemas/WarehouseResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction")
     *         )
     *     )
     * )
     */
    public function getWarehousesOrderedBy($column, $direction)
    {
        return $this->warehouseRepository->orderBy($column, $direction);
    }

    /**
     * @OA\Put(
     *     path="/api/warehouses/{id}",
     *     summary="Update a warehouse",
     *     tags={"Warehouse"},
     *      security={{"bearerAuth": {} }},
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
     *         name="amount",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *
     *     @OA\Parameter(
     *         name="pure_price",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="number", format="float", example=225.5)
     *     ),
     *
     *     @OA\Parameter(
     *         name="payment_date",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-09-10")
     *     ),
     *
     *     @OA\Parameter(
     *         name="settlement_date",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-09-24")
     *     ),
     *
     *     @OA\Parameter(
     *         name="expiry_date",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
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
     *         response=200,
     *         description="Warehouse updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseResource")
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
        $this->validateWarehouseData($data, $warehouse, 'sometimes');

        return $this->warehouseRepository->update($warehouse, $data);
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
        return $this->warehouseRepository->delete($warehouse);
    }

    protected function validateWarehouseData(array $data, $warehouse = null, $rule = 'required')
    {
        $validator = Validator::make($data, [
            'pure_price' => "$rule|numeric|min:0",
            'amount' => "$rule|numeric|min:0",
            'payment_date' => "$rule|date",
            'settlement_date' => 'nullable|date|after_or_equal:payment_date',
            'expiry_date' => "$rule|date|after_or_equal:payment_date",
            'product_id' => "$rule|exists:products,id",
        ]);

        $validator->after(function ($validator) use ($data, $warehouse) {
            $expiryDate = $data['expiry_date'] ?? ($warehouse ? $warehouse->expiry_date : null);
            if (! empty($data['settlement_date']) && ! empty($expiryDate)) {
                if (strtotime($data['settlement_date']) > strtotime($expiryDate)) {
                    $validator->errors()->add('settlement_date', 'The settlement date must be before or equal to the expiry date ('.$expiryDate.').');
                }
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
