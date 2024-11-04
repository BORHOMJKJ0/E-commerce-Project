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
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *    @OA\Property(property="successful", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Cart returned successfully"),
     *      @OA\Property(property="status_code", type="integer", example=200),
     *
     *         @OA\Items(ref="#/components/schemas/CartResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found",
     *
     *         @OA\JsonContent(
     *
     *    @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cart not found"),
     *            @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function getCartById()
    {
        $cart = Cart::where('user_id', auth()->id())->first();
        $data = ['Cart' => CartResource::make($cart)];

        return ResponseHelper::jsonResponse($data, 'Cart retrieved successfully!');
    }

    /**
     * @OA\Post(
     *     path="/api/carts",
     *     summary="Create a Cart",
     *     tags={"Carts"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *             )
     *         )
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
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="price", type="number", format="float", example=19.99),
     *         @OA\Property(property="successful", type="boolean", example=true),
     *               @OA\Property(property="message", type="string", example="Cart created successfully"),
     *       @OA\Property(property="status_code", type="integer", example=200)
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
     *    @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid input data"),
     *            @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     )
     * )
     */
    public function createCart(): void
    {
        $carts = Cart::where('user_id', auth()->id())->get();
        if ($carts->isEmpty()) {
            $cart = $this->cartRepository->create();
            $data = [
                'Cart' => CartResource::make($cart),
            ];
        }
    }

    /**
     * @OA\Put(
     *     path="/api/carts/{id}",
     *     summary="Update a cart",
     *     tags={"Carts"},
     *     security={{"bearerAuth": {}}},
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
     *         description="Cart updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Vegetables"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="name", type="string", example="Smartphone"),
     *                     @OA\Property(property="user", type="string", example="Hasan Zaeter"),
     *         @OA\Property(property="successful", type="boolean", example=true),
     *               @OA\Property(property="message", type="string", example="Cart updated successfully"),
     *       @OA\Property(property="status_code", type="integer", example=200)
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
     *    @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid input data"),
     *            @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *    @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to update this Cart ."),
     *            @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found",
     *
     *         @OA\JsonContent(
     *
     *    @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cart not found"),
     *            @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function updateCart()
    {
        try {
            $cart = Cart::where('user_id', auth()->id())->first();
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
     *     @OA\Response(
     *         response=200,
     *         description="Cart deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *    @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart deleted successfully"),
     *            @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *    @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to delete this Cart."),
     *           @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found",
     *
     *         @OA\JsonContent(
     *
     *    @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cart not found"),
     *          @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function deleteCart()
    {
        try {
            $cart = Cart::where('user_id', auth()->id())->first();
            $this->checkOwnership($cart, 'Cart', 'delete');
            $this->cartRepository->delete($cart);
            $response = ResponseHelper::jsonResponse([], 'Cart deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }
}
