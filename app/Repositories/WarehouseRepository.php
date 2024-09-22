<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function getAll($items, $page)
    {
        return Warehouse::paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Warehouse::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
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
