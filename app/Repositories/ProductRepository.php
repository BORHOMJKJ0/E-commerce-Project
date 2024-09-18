<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll()
    {
        return Product::with(['category', 'user'])->paginate(5);
    }

    public function findById($id)
    {
        return Product::with(['category', 'user'])->findOrFail($id);
    }

    public function orderBy($column, $direction)
    {
        return Product::with(['category', 'user'])->orderBy($column, $direction)->paginate(5);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);

        return $product;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        return $product->delete();
    }
}
