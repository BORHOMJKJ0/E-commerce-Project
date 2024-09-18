<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAll()
    {
        return Category::paginate(5);
    }

    public function orderBy($column, $direction)
    {
        return Category::orderBy($column, $direction)->paginate(5);
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
