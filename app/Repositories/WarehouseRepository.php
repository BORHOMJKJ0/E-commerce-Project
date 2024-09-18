<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function getAll()
    {
        return Warehouse::with('product')->all();
    }

    public function findById($id)
    {
        return Warehouse::with('product')->findOrFail($id);
    }

    public function orderBy($column, $direction)
    {
        return Warehouse::with('product')->orderBy($column, $direction)->get();
    }

    public function create(array $data)
    {
        return Warehouse::create($data);
    }

    public function update($id, array $data)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($data);

        return $warehouse;
    }

    public function delete($id)
    {
        $warehouse = Warehouse::findOrFail($id);

        return $warehouse->delete();
    }
}
