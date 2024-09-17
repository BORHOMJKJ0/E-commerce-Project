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

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     )
     * )
     */
    public function getAllProducts()
    {
        return $this->productRepository->getAll();
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a single product by ID",
     *     tags={"Products"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function getProductById($id)
    {
        return $this->productRepository->findById($id);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "image", "price", "category_id", "user_id"},
     *                 @OA\Property(property="name", type="string", example="Perform"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="price", type="number", format="float", example=250.75),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *         @OA\Schema(type="string", example="multipart/form-data")
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     * )
     */
    public function createProduct(array $data)
    {
        $this->validateProductData($data);

        return $this->productRepository->create($data);
    }

    /**
     * @OA\Get(
     *     path="/api/products/order/{column}/{direction}",
     *     summary="Order products by a specific column",
     *     tags={"Products"},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"name", "price", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction")
     *         )
     *     )
     * )
     */
    public function getProductsOrderedBy($column, $direction)
    {
        return $this->productRepository->orderBy($column, $direction);
    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="Perform")
     *     ),
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=50.75)
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function updateProduct($id, array $data)
    {
        $product = $this->productRepository->findById($id);

        $this->validateProductData($data, 'sometimes');

        return $this->productRepository->update($id, $data);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
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
