<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\CartItemsResource;
use App\Models\Cart;
use App\Models\Cart_items;
use App\Repositories\CartItemsRepository;
use App\Traits\AuthTrait;
use App\Traits\ValidationTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Cart_Items_Service
{
    use AuthTrait,ValidationTrait;

    protected $cartItemsRepository;

    public function __construct(CartItemsRepository $cartItemsRepository)
    {
        $this->cartItemsRepository = $cartItemsRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/cart_items",
     *     summary="Get my Cart_items",
     *     tags={"Cart_items"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CartItemsResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function getAllCart_items(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $cart_items = $this->cartItemsRepository->getAll($items, $page);
        $hasMorePages = $cart_items->hasMorePages();

        $data = [
            'Cart_items' => CartItemsResource::collection($cart_items),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Cart_items retrieved successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/cart_items/{id}",
     *     summary="Get a Cart_items by ID",
     *     tags={"Cart_items"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="your Cart_items ID you want to show it",
     *
     *        @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CartItemsResource")
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to view this Cart_items.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="cart not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="cart not found")
     *         )
     *     )
     * )
     */
    public function getCart_itemsById(Cart_items $cart_items)
    {
        try {
            $cart = $cart_items->cart;
            if (!$cart) {
                \Log::error('Cart not found for Cart_items ID: ' . $cart_items->id);
                throw new HttpResponseException(
                    ResponseHelper::jsonResponse([], 'Cart not found', 404, false)
                );
            }
            $this->checkOwnership($cart, 'Cart_items', 'perform');
            $data = ['Cart_items' => CartItemsResource::make($cart_items)];
            $response = ResponseHelper::jsonResponse($data, 'Cart_items retrieved successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }



    /**
     * @OA\Post(
     *     path="/api/cart_items",
     *     summary="Create a Cart_items",
     *     tags={"Cart_items"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *      @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *             required={"amount", "expiry_date", "cart_id"},
     *
     *             @OA\Property(property="amount", type="number", example=100,description="Cart_items Amount"),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01",description="Cart_items exoiry date"),
     *             @OA\Property(property="cart_id", type="integer", example=1,description="cart ID that you want to add this Cart_items to it")
     *             )
     *         )
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
     *         response=201,
     *         description="Cart_items created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CartItemsResource")
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to create this Cart_items .")
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
    public function createCart_items(array $data)
    {
        try {
            $this->validate_Cart_items_Data($data);
            $cart_items = $this->cartItemsRepository->create($data);
            $data = ['Cart_items' => CartItemsResource::make($cart_items)];
            $response = ResponseHelper::jsonResponse($data, 'Cart_items created successfully!', 201);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/cart_items/order/{column}/{direction}",
     *     summary="Order My Cart_items by a specific column",
     *     tags={"Cart_items"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *     description="Column you want to order the Cart_items by it",
     *
     *         @OA\Schema(type="string", enum={"quantity", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *     description="Dircetion of ordering",
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *    @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page ",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CartItemsResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction or parameters")
     *         )
     *     )
     * )
     */
    public function getCart_items_OrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['quantity', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $cart_items = $this->cartItemsRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $cart_items->hasMorePages();

        $data = [
            'Cart_items' => CartItemsResource::collection($cart_items),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Cart_items ordered successfully');

    }

    /**
     * @OA\Put(
     *     path="/api/cart_items/{id}",
     *     summary="Update a Cart_items",
     *     tags={"Cart_items"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="your Cart_items ID that you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=false,
     *     description="THe amount of this Cart_items",
     *
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *
     *     @OA\Parameter(
     *         name="cart_id",
     *         in="query",
     *         required=false,
     *     description="cart ID of this Cart_items",
     *
     *         @OA\Schema(type="integer", example=1)
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
     *         description="Cart_items updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="amount", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(
     *                 property="cart",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Iphone 15"),
     *                 @OA\Property(property="price", type="number", format="float", example="499.99"),
     *                 @OA\Property(property="category", type="string", example="Smartphone"),
     *                 @OA\Property(property="user", type="string", example="Hasan Zaeter")
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
     *  @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this Cart_items .")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart_items not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Cart_items not found")
     *         )
     *     )
     * )
     */
    public function updateCart_items(Cart_items $cart_items, array $data)
    {
        try {
            $this->validate_Cart_items_Data($data, 'sometimes', 0);
            $cart = $cart_items->cart;
            $this->checkOwnership($cart, 'Cart_items', 'update');
            $cart_items = $this->cartItemsRepository->update($cart_items, $data);
            $data = ['Cart_items' => CartItemsResource::make($cart_items)];
            $response = ResponseHelper::jsonResponse($data, 'Cart_items updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/cart_items/{id}",
     *     summary="Delete a Cart_items",
     *     tags={"Cart_items"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="your Cart_items ID you want to delete it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Cart_items deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Cart_items deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this Cart_items .")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart_items not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Cart_items not found")
     *         )
     *     )
     * )
     */
    public function deleteCart_items(Cart_items $cart_items)
    {
        try {
            $cart = $cart_items->cart;
            $this->checkOwnership($cart, 'Cart_items', 'delete');
            $this->cartItemsRepository->delete($cart_items);
            $response = ResponseHelper::jsonResponse([], 'Cart_items deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validate_Cart_items_Data(array $data, $rule = 'required', $limit = 1)
    {
        $validator = Validator::make($data, [
            'quantity' => "$rule|numeric|min:$limit",
            'product_id' => "$rule|exists:products,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
