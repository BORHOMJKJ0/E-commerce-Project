<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll()
    {
        return Product::paginate(5);
    }

    public function orderBy($column, $direction)
    {
        return Product::orderBy($column, $direction)->paginate(5);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data)
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}
