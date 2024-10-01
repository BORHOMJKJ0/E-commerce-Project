<?php

namespace App\Repositories;

use App\Models\Category;
use App\Traits\Lockable;

class CategoryRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Category::paginate($items, ['*'], 'page', $page);
    }

    public function getMy($items, $page)
    {
        return Category::whereHas('products', function ($query) {
            $query->where('user_id', auth()->id());
        })->paginate($items, ['*'], 'page', $page);
    }

    public function orderMyBy($column, $direction, $page, $items)
    {
        return Category::whereHas('products', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Category::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return $this->lockForCreate(function () use ($data) {
            return Category::create($data);
        });
    }

    public function update(Category $category, array $data)
    {
        return $this->lockForUpdate(Category::class, $category->id, function ($lockedCategory) use ($data) {
            $lockedCategory->update($data);

            return $lockedCategory;
        });
    }

    public function delete(Category $category)
    {
        return $this->lockForDelete(Category::class, $category->id, function ($lockedCategory) {
            return $lockedCategory->delete();
        });
    }
}
