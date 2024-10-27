<?php

namespace App\Repositories;

use App\Http\Requests\SearchProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
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

    public function getProductsByFilters(SearchProductRequest $request, $items, $page)
    {
        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->has('category')) {
            $categoryIds = Category::where('name', 'like', '%'.$request->category.'%')->pluck('id')->toArray();
            if (! empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // if ($request->has('expire_date')) {
        //     $productsId =  Warehouse::whereNotNull('expiry_date')->where('expiry_date', '<=', $request->expire_date)
        //         ->where('amount', '>', 0)
        //         ->groupBy('product_id')
        //         ->pluck('product_id')
        //         ->toArray();

        //     $query->whereIn('id', $productsId);
        // }
        $products = $query->paginate($items, ['*'], 'page', $page);

        if (! empty($products)) {
            return $products;
        } else {
            return null;
        }
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
