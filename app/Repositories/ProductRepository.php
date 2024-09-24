<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll($items, $page)
    {
        return Product::paginate($items, ['*'], 'page', $page);
    }

    public function getMy($items, $page)
    {
        return Product::where('user_id', auth()->user()->id)
            ->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Product::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function orderMyBy($column, $direction, $page, $items)
    {
        return Product::where('user_id', auth()->user()->id)
            ->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
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
