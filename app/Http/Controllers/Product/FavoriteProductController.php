<?php

namespace App\Http\Controllers\Product;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\UserRepository;
use App\Services\FavoriteProductService;
use Illuminate\Http\JsonResponse;

class FavoriteProductController extends Controller
{
    private $service;

    public function __construct(FavoriteProductService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        return $this->service->index();
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
