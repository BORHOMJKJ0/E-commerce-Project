<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->middleware('auth:api');
        $this->cartService = $cartService;
    }

    public function store(): JsonResponse
    {
        return $this->cartService->createCart();
    }

    public function update(Cart $cart)
    {
        return $this->cartService->updateCart($cart);
    }

    public function show(Cart $Cart): JsonResponse
    {
        return $this->cartService->getCartById($Cart);
    }

    public function destroy(Cart $Cart): JsonResponse
    {
        return $this->cartService->deleteCart($Cart);
    }
}
