<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FavoriteProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteProductController extends Controller
{
    private $service;

    public function __construct(FavoriteProductService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        return $this->service->index($request);
    }

    public function store(Product $product): JsonResponse
    {
        return $this->service->store($product->id);
    }

    public function destroy(Product $product): JsonResponse
    {
        return $this->service->destroy($product->id);
    }
}
