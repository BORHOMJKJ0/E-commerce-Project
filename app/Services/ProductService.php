<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAll();
    }

    public function getProductById($id)
    {
        return $this->productRepository->findById($id);
    }

    public function createProduct(array $data)
    {
        $this->validateProductData($data);

        return $this->productRepository->create($data);
    }

    public function getProductsOrderedBy($column, $direction)
    {
        return $this->productRepository->orderBy($column, $direction);
    }

    public function updateProduct($id, array $data)
    {
        $product = $this->productRepository->findById($id);

        $this->validateProductData($data, 'sometimes');

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }

    protected function validateProductData(array $data, $rule = 'required')
    {
        $validator = Validator::make($data, [
            'name' => "$rule|string|max:255|unique:products,name",
            'image' => "$rule|image|max:5120",
            'price' => "$rule|numeric|min:0",
            'category_id' => "$rule|exists:categories,id",
            'user_id' => "$rule|exists:users,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
