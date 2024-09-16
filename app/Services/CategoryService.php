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

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CategoryResource")
     *         )
     *     )
     * )
     */
    public function getAllCategories()
    {
        return $this->categoryRepository->getAll();
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get a category by ID",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function getCategoryById($id)
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a category",
     *     tags={"Categories"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="fruits")
     *         )
     *     ),
     *
     *    @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     * )
     */
    public function createCategory(array $data)
    {
        $this->validateCategoryData($data);

        return $this->categoryRepository->create($data);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/order/{column}/{direction}",
     *     summary="Order categories by a specific column",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"name", "created_at", "updated_at"})
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
     *             @OA\Items(ref="#/components/schemas/CategoryResource")
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
    public function getCategoriesOrderedBy($column, $direction)
    {
        return $this->categoryRepository->orderBy($column, $direction);
    }

    /**
     * @OA\Patch(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="Updated Category Name")
     *     ),
     *
     *      @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function updateCategory($id, array $data)
    {
        $category = $this->categoryRepository->findById($id);

        $this->validateCategoryData($data);

        return $this->categoryRepository->update($id, $data);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Category deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
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
