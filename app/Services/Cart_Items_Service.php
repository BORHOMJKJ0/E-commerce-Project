<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\CartItemsResource;
use App\Models\Cart;
use App\Models\Cart_items;
use App\Models\Warehouse;
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
     *    @OA\Property(property="successful", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Cart_items returned successfully"),
     *      @OA\Property(property="status_code", type="integer", example=200),
     *
     *            @OA\Items(ref="#/components/schemas/CartItemsResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *               @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid parameters"),
     *     @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     )
     * )
     */
    public function getAllCart_items(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $cart_item = $this->cartItemsRepository->getAll($items, $page);
        $hasMorePages = $cart_item->hasMorePages();

        $data = [
            'Cart_items' => CartItemsResource::collection($cart_item),
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
     *    @OA\Property(property="successful", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Cart_item returned successfully"),
     *      @OA\Property(property="status_code", type="integer", example=200),
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
     *             @OA\Property(property="message", type="string", example="You are not authorized to view this Cart_items.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="cart not found",
     *
     *         @OA\JsonContent(
     *
     *     @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="cart not found"),
     *     @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function getCart_itemById(Cart_items $cart_item)
    {
        try {
            $cart = Cart::where('id', $cart_item->cart_id)->first();

            $this->checkOwnership($cart, 'Cart_items', 'perform');

            $data = ['Cart_items' => CartItemsResource::make($cart_item)];

            $response = ResponseHelper::jsonResponse($data, 'Cart_item retrieved successfully!');
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
     *             required={"quantity", "warehouse_id"},
     *
     *             @OA\Property(property="quantity", type="number", example=100,description="Cart_items quantity"),
     *             @OA\Property(property="warehouse_id", type="integer", example=1,description="warehouse ID that you want to add this Cart_items from it")
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
     *    @OA\Property(property="successful", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Cart_item created successfully"),
     *      @OA\Property(property="status_code", type="integer", example=200),
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
     *     @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to create this Cart_items ."),
     *     @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *     @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid input data"),
     *     @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     * )
     */
    public function createCart_items(array $data)
    {
        try {
            $this->validate_Cart_items_Data($data);
            $warehouse = Warehouse::where('id', $data['warehouse_id'])->first();
            $this->checkAmount($data, $warehouse);
            $cart_item = $this->cartItemsRepository->create($data);
            $data = ['Cart_items' => CartItemsResource::make($cart_item)];
            $response = ResponseHelper::jsonResponse($data, 'Cart_item created successfully!', 201);
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
     *    @OA\Property(property="successful", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Cart_items returned successfully"),
     *      @OA\Property(property="status_code", type="integer", example=200),
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
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid column or direction or parameters"),
     *@OA\Property(property="status_code", type="integer", example=400)
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
        $cart_item = $this->cartItemsRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $cart_item->hasMorePages();

        $data = [
            'Cart_items' => CartItemsResource::collection($cart_item),
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
     *         name="quantity",
     *         in="query",
     *         required=false,
     *     description="The quantity of this Cart_items",
     *
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *
     *     @OA\Parameter(
     *         name="warehouse_id",
     *         in="query",
     *         required=false,
     *     description="warehouse ID of this Cart_items",
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
     *                 @OA\Property(property="user", type="string", example="Hasan Zaeter"),
     *    @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart_item updated successfully"),
     *     @OA\Property(property="status_code", type="integer", example=200)
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
     *     @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid input data"),
     *     @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     *
     *  @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *     @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to delete this Cart_items ."),
     *     @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart_items not found",
     *
     *         @OA\JsonContent(
     *
     *@OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cart_items not found"),
     *     @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function updateCart_items(Cart_items $cart_item, array $data)
    {
        try {
            $this->validate_Cart_items_Data($data, 'sometimes', 0);
            $cart = Cart::where('id', $cart_item->cart_id)->first();
            $this->checkOwnership($cart, 'Cart_items', 'update');
            $warehouse_id = $data['warehouse_id'] ?? $cart_item->warehouse->id;
            $warehouse = Warehouse::where('id', $warehouse_id)->first();
            $this->checkAmount($data, $warehouse);
            $cart_item = $this->cartItemsRepository->update($cart_item, $data);
            $data = ['Cart_items' => CartItemsResource::make($cart_item)];
            $response = ResponseHelper::jsonResponse($data, 'Cart_item updated successfully!');
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
     *     @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart_item deleted successfully"),
     *     @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *     @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to delete this Cart_items ."),
     *     @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Cart_items not found",
     *
     *         @OA\JsonContent(
     *
     *@OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cart_items not found"),
     *     @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function deleteCart_items(Cart_items $cart_item)
    {
        try {
            $cart = Cart::where('id', $cart_item->cart_id)->first();
            $this->checkOwnership($cart, 'Cart_items', 'delete');
            $this->cartItemsRepository->delete($cart_item);
            $response = ResponseHelper::jsonResponse([], 'Cart_item deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validate_Cart_items_Data(array $data, $rule = 'required', $limit = 1)
    {
        $validator = Validator::make($data, [
            'quantity' => "$rule|numeric|min:$limit",
            'warehouse_id' => "$rule|exists:warehouses,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
