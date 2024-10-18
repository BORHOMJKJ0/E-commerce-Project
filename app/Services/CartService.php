<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Repositories\CartRepository;
use App\Traits\AuthTrait;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartService
{
    use AuthTrait;

    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Enter JWT Bearer token in the format 'Bearer {token}'"
     * )
     */
    /**
     * @OA\Get(
     *     path="/api/carts/{id}",
     *     summary="Get a single cart by ID",
     *     tags={"Carts"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Cart ID you want to show it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CartResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Cart not found")
     *         )
     *     )
     * )
     */
    public function getCartById(Cart $cart)
    {
        $this->checkOwnership($cart, 'Cart', 'show');
        $data = ['Cart' => CartResource::make($cart)];

        return ResponseHelper::jsonResponse($data, 'Cart retrieved successfully!');
    }

    /**
     * @OA\Post(
     *     path="/api/carts",
     *     summary="Create a Cart",
     *     tags={"Carts"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *      @OA\MediaType(
     *             mediaType="multipart/form-data",
     *    ),
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
     *         description="Cart created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user", type="string", example="Electronic devices"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 example={},
     *
     *             @OA\Items()
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     * )
     */
    public function createCart()
    {
        $carts = Cart::where('user_id', auth()->id())->get();
        if ($carts->isEmpty()) {
            $cart = $this->cartRepository->create();
            $data = [
                'Cart' => CartResource::make($cart),
            ];

            return ResponseHelper::jsonResponse($data, 'Cart created successfully!', 201);
        } else {
            return ResponseHelper::jsonResponse([], 'You already have a cart ', 200);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Category ID you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *     description="Category name",
     *
     *         @OA\Schema(type="string", example="Vegetables")
     *     ),
     *
     *     @OA\Header(
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
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Electronic devices"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="name", type="string", example="Smartphone"),
     *                     @OA\Property(property="user", type="string", example="Hasan Zaeter")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You cannot update category with associated products.")
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
    public function updateCart(Cart $cart)
    {
        try {
            $this->checkOwnership($cart, 'Cart', 'update');
            $cart = $this->cartRepository->update($cart);
            $data = [
                'Cart' => CartResource::make($cart),
            ];

            $response = ResponseHelper::jsonResponse($data, 'Cart updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/carts/{id}",
     *     summary="Delete a Cart",
     *     tags={"Carts"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Cart ID you want to delete it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Cart deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Cart deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this Cart.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Cart not found")
     *         )
     *     )
     * )
     */
    public function deleteCart(Cart $cart)
    {
        try {
            $this->checkOwnership($cart, 'Cart', 'delete');
            $this->cartRepository->delete($cart);
            $response = ResponseHelper::jsonResponse([], 'Cart deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }
}
