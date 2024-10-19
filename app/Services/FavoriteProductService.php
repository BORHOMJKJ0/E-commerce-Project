<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

class FavoriteProductService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(): JsonResponse
    {
        $user = $this->userRepository->findById(auth()->user()->id);
        $favoriteProducts = $user->favoriteProducts()->with(['images', 'category'])->get();
        $data = ['products' => ProductResource::collection($favoriteProducts)];

        return ResponseHelper::jsonResponse($data, 'retrieve all favorite products');
    }

    public function store($product_id): JsonResponse
    {
        $user = $this->userRepository->findById(auth()->user()->id);
        $user->favoriteProducts()->attach($product_id);

        return ResponseHelper::jsonResponse([], 'Product added to favorites', 201);

    }

    public function destroy($product_id): JsonResponse
    {
        $user = $this->userRepository->findById(auth()->user()->id);
        $user->favoriteProducts()->detach($product_id);

        return ResponseHelper::jsonResponse([], 'Product removed from favorites');
    }
}
