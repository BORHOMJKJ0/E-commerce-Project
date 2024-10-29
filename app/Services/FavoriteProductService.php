<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ProductResource;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteProductService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/products/favorites/index",
     *     summary="Retrieve all favorite products",
     *     description="Get a paginated list of the user's favorite products with details, images, category, and other associated data.",
     *     tags={"Favorite Products"},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *
     * @OA\Response(
     *      response=200,
     *      description="Successful response",
     *
     *      @OA\JsonContent(
     *          type="object",
     *
     *          @OA\Property(property="successful", type="boolean", example=true),
     *          @OA\Property(property="message", type="string", example="retrieve all favorite products"),
     *          @OA\Property(property="data", type="array",
     *
     *              @OA\Items(ref="#/components/schemas/ProductResource")
     *          ),
     *
     *          @OA\Property(property="status_code", type="integer", example=200)
     *      )
     *  )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $user = $this->userRepository->findById(auth()->user()->id);
        $favoriteProducts = $user->favoriteProducts()->with(['images', 'category'])->paginate($items, ['*'], 'page', $page);
        $hasMorePages = $favoriteProducts->hasMorePages();
        $data = [
            'products' => ProductResource::collection($favoriteProducts),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'retrieve all favorite products');
    }

    /**
     * @OA\Post(
     *     path="api/products/favorites/store/{product_id}",
     *     summary="Add a product to the user's favorites",
     *     description="Adds the specified product to the authenticated user's list of favorite products.",
     *     tags={"Favorite Products"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         description="ID of the product to add to favorites",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product added to favorites successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product added to favorites"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product Not Found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function store($product_id): JsonResponse
    {
        $user = $this->userRepository->findById(auth()->user()->id);
        $user->favoriteProducts()->attach($product_id);

        return ResponseHelper::jsonResponse([], 'Product added to favorites');
    }

    /**
     * @OA\Delete(
     *     path="api/products/favorites/destroy/{product_id}",
     *     summary="Remove a product from the user's favorites",
     *     description="Removes the specified product from the authenticated user's list of favorite products.",
     *     tags={"Favorite Products"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         description="ID of the product to remove from favorites",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product removed from favorites successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product removed from favorites"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found in favorites",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product Not Found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function destroy($product_id): JsonResponse
    {
        $user = $this->userRepository->findById(auth()->user()->id);
        $user->favoriteProducts()->detach($product_id);

        return ResponseHelper::jsonResponse([], 'Product removed from favorites');
    }
}
