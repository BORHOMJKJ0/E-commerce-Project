<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function getAll($items, $page)
    {
        return Warehouse::whereHas('product', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Warehouse::whereHas('product', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return Warehouse::create($data);
    }

    public function update(Warehouse $warehouse, array $data)
    {
        $warehouse->update($data);

        return $warehouse;
    }

    public function delete(Warehouse $warehouse)
    {
        return $warehouse->delete();
    }
}
