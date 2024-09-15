<?php

namespace App\Services;

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

    public function getAllWarehouses()
    {
        return $this->warehouseRepository->getAll();
    }

    public function getWarehouseById($id)
    {
        return $this->warehouseRepository->findById($id);
    }

    public function createWarehouse(array $data)
    {
        $data['settlement_date'] = null;

        $this->validateWarehouseData($data);

        return $this->warehouseRepository->create($data);
    }

    public function getWarehousesOrderedBy($column, $direction)
    {
        return $this->warehouseRepository->orderBy($column, $direction);
    }

    public function updateWarehouse($id, array $data)
    {
        $warehouse = $this->warehouseRepository->findById($id);
        if (isset($data['expiry_date'])) {
            throw ValidationException::withMessages([
                'expiry_date' => 'You cannot update the expiry date once it has been set.',
            ]);
        }
        $this->validateWarehouseData($data, $warehouse, 'sometimes');

        return $this->warehouseRepository->update($id, $data);
    }

    public function deleteWarehouse($id)
    {
        return $this->warehouseRepository->delete($id);
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
