<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAll($items, $page)
    {
        return Category::paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Category::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category)
    {
        return $category->delete();
    }
}
