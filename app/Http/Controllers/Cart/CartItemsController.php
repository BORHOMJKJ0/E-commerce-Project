<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchProductRequest;
use App\Models\Cart_items;
use App\Services\Cart_Items_Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartItemsController extends Controller
{
    protected $cart_items_Service;

    public function __construct(Cart_Items_Service $cart_items_Service)
    {
        $this->middleware('auth:api');
        $this->cart_items_Service = $cart_items_Service;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->cart_items_Service->getAllCart_items($request);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->cart_items_Service->getCart_items_OrderedBy($column, $direction, $request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->cart_items_Service->createCart_items($request->all());
    }

    public function searchByFilters(SearchProductRequest $request)
    {
        return $this->cart_items_Service->searchByFilters($request);
    }

    public function update(Cart_items $cart, Request $request)
    {
        return $this->cart_items_Service->updateCart_items($cart, $request->all());
    }

    public function show(Cart_items $Cart): JsonResponse
    {
        return $this->cart_items_Service->getCart_itemsById($Cart);
    }

    public function destroy(Cart_items $Cart): JsonResponse
    {
        return $this->cart_items_Service->deleteCart_items($Cart);
    }
}
