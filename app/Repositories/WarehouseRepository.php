<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Traits\Lockable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WarehouseRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Warehouse::paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Warehouse::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function getProductWithOffers($items, $page): LengthAwarePaginator
    {
        return Warehouse::with(['product.user', 'offers' => function ($query) {
            $query->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now());
        }])->whereHas('offers', function ($query) {
            $query->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now());
        })
            ->paginate($items, ['*'], 'page', $page);
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
