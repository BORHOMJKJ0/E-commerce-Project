<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function getAll()
    {
        return Warehouse::paginate(5);
    }

    public function orderBy($column, $direction)
    {
        return Warehouse::orderBy($column, $direction)->get();
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
