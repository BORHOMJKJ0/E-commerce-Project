<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Traits\Lockable;

class WarehouseRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Warehouse::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Warehouse::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return $this->lockForCreate(function () use ($data) {
            return Warehouse::create($data);
        });
    }

    public function update(Warehouse $warehouse, array $data)
    {
        return $this->lockForUpdate(Warehouse::class, $warehouse->id, function ($lockedWarehouse) use ($data) {
            $lockedWarehouse->update($data);

            return $lockedWarehouse;
        });
    }

    public function delete(Warehouse $warehouse)
    {
        return $this->lockForDelete(Warehouse::class, $warehouse->id, function ($lockedWarehouse) {
            return $lockedWarehouse->delete();
        });
    }
}
