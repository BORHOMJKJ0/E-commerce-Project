<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
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

    public function update()
    {
        return $this->cartService->updateCart();
    }

    public function show(): JsonResponse
    {
        return $this->cartService->getCartById();
    }

    public function destroy(): JsonResponse
    {
        return $this->cartService->deleteCart();
    }
}
