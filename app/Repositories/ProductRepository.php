<?php

namespace App\Repositories;

use App\Models\Product;
use App\Traits\Lockable;

class ProductRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Product::paginate($items, ['*'], 'page', $page);
    }

    public function getMy($items, $page)
    {
        return Product::where('user_id', auth()->id())
            ->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Product::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function orderMyBy($column, $direction, $page, $items)
    {
        return Product::where('user_id', auth()->id())
            ->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return $this->lockForCreate(function () use ($data) {
            return Product::create($data);
        });
    }

    public function update(Product $product, array $data)
    {
        return $this->lockForUpdate(Product::class, $product->id, function ($lockedProduct) use ($data) {
            $lockedProduct->update($data);

            return $lockedProduct;
        });
    }

    public function delete(Product $product)
    {
        return $this->lockForDelete(Product::class, $product->id, function ($lockedProduct) {
            return $lockedProduct->delete();
        });
    }
}
