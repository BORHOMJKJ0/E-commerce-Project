<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->getAll();
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(array $data)
    {
        $this->validateCategoryData($data);

        return $this->categoryRepository->create($data);
    }

    public function getCategoriesOrderedBy($column, $direction)
    {
        return $this->categoryRepository->orderBy($column, $direction);
    }

    public function updateCategory($id, array $data)
    {
        $category = $this->categoryRepository->findById($id);

        $this->validateCategoryData($data);

        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepository->delete($id);
    }

    protected function validateCategoryData(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100|unique:categories,name',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
