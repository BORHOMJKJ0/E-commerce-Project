<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\Product;
use App\Repositories\ImageRepository;
use App\Traits\AuthTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImageService
{
    use AuthTrait;

    protected $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
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
     *     path="/api/images",
     *     summary="Get all Images",
     *     tags={"Images"},
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
     *             @OA\Items(ref="#/components/schemas/ImageResource")
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
    public function getAllImages(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $images = $this->imageRepository->getAll($items, $page);
        $hasMorePages = $images->hasMorePages();

        $data = [
            'Images' => ImageResource::collection($images),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Images retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/images/my",
     *     summary="Get My Images",
     *     tags={"Images"},
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
     *             @OA\Items(ref="#/components/schemas/ImageResource")
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
    public function getMyImages(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $images = $this->imageRepository->getMy($items, $page);
        $hasMorePages = $images->hasMorePages();

        $data = [
            'Images' => ImageResource::collection($images),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Images retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/images/{id}",
     *     summary="Get a Image by ID",
     *     tags={"Images"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Image you want to show it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ImageResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Image not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Image not found")
     *         )
     *     )
     * )
     */
    public function getImageById(Image $image)
    {
        $data = ['Image' => ImageResource::make($image)];

        return ResponseHelper::jsonResponse($data, 'Image retrieved successfully!');
    }

    /**
     * @OA\Post(
     *     path="/api/images",
     *     summary="Create an Image",
     *     tags={"Images"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"image", "product_id", "main"},
     *
     *                 @OA\Property(property="product_id", type="integer", example=1, description="Product ID that the image belongs to"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Product Image"),
     *                 @OA\Property(property="main", type="boolean", example=false, description="Whether the image is the main image for the product")
     *             )
     *         )
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
     *         response=201,
     *         description="Image created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=22, description="The ID of the image"),
     *             @OA\Property(property="image", type="string", description="The path to the product image"),
     *             @OA\Property(property="product_id", type="integer", example=1, description="The ID of the product the image belongs to"),
     *             @OA\Property(property="main", type="boolean", example=false, description="Whether the image is the main image for the product"),
     *             @OA\Property(property="product", type="object", description="Product details associated with the image",
     *                 @OA\Property(property="id", type="integer", example=1, description="The ID of the product"),
     *                 @OA\Property(property="name", type="string", example="banana", description="The name of the product"),
     *                 @OA\Property(property="price", type="number", format="float", example=22021320, description="The price of the product"),
     *                 @OA\Property(property="description", type="string", example="This is a fantastic product", description="The description of the product"),
     *                 @OA\Property(property="category", type="string", example="tempore est", description="The category of the product"),
     *                 @OA\Property(property="user", type="string", example="Hasan Zaeter", description="The owner of the product")
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
     * )
     */
    public function createImage(array $data)
    {
        try {
            if (isset($data['main'])) {
                $data['main'] = filter_var($data['main'], FILTER_VALIDATE_BOOLEAN);
            }
            $this->validateImageData($data);

            $product = Product::find($data['product_id']);
            $this->checkOwnership($product, 'Image', 'create');

            $hasMainImage = Image::where('product_id', $data['product_id'])
                ->where('main', 1)
                ->exists();

            if ($hasMainImage && isset($data['main']) && $data['main'] == 1) {
                return ResponseHelper::jsonResponse([], 'This product already has a main image.', 400);
            }

            $image = $this->imageRepository->create($data);
            $data = [
                'Image' => ImageResource::make($image),
            ];

            $response = ResponseHelper::jsonResponse($data, 'Image created successfully!', 201);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/images/order/{column}/{direction}",
     *     summary="Order images by a specific column",
     *     tags={"Images"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *         description="Column you want to order the images by it",
     *
     *         @OA\Schema(type="string", enum={"created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *        description="Dircetion of ordering",
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
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
     *             @OA\Items(ref="#/components/schemas/ImageResource")
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
    public function getImagesOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];
        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $images = $this->imageRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $images->hasMorePages();
        $data = [
            'Images' => ImageResource::collection($images),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Images ordered successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/images/my/order/{column}/{direction}",
     *     summary="Order My images by a specific column",
     *     tags={"Images"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *     description="Column you want to order the images by it",
     *
     *         @OA\Schema(type="string", enum={"created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *        description="Dircetion of ordering",
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
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
     *             @OA\Items(ref="#/components/schemas/ImageResource")
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
    public function getMyImagesOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];
        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $images = $this->imageRepository->orderMyBy($column, $direction, $page, $items);
        $hasMorePages = $images->hasMorePages();
        $data = [
            'Images' => ImageResource::collection($images),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Images ordered successfully!');
    }

    /**
     * @OA\Put(
     *     path="/api/images/{id}",
     *     summary="Update a Image",
     *     tags={"Images"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Image ID you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=false,
     *     description="Product Image",
     *
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *
     *     @OA\Parameter(
     *         name="main",
     *         in="query",
     *         required=false,
     *     description="Whether the image is the main image for the product",
     *
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=false,
     *     description="Product ID that image belong to it",
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
     *         response=201,
     *         description="Image created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=22, description="The ID of the image"),
     *             @OA\Property(property="image", type="string", description="The path to the product image"),
     *             @OA\Property(property="main", type="boolean", example=false, description="Whether the image is the main image for the product"),
     *             @OA\Property(property="product_id", type="integer", example=2, description="The ID of the product the image belongs to"),
     *             @OA\Property(property="product", type="object", description="Product details associated with the image",
     *                 @OA\Property(property="id", type="integer", example=2, description="The ID of the product"),
     *                 @OA\Property(property="name", type="string", example="meat", description="The name of the product"),
     *                 @OA\Property(property="price", type="number", format="float", example=200, description="The price of the product"),
     *                 @OA\Property(property="description", type="string", example="This is a greate product", description="The description of the product"),
     *                 @OA\Property(property="category", type="string", example="hakunamatata", description="The category of the product"),
     *                 @OA\Property(property="user", type="string", example="Muhammad Aydi", description="The owner of the product")
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
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You cannot update Image with associated products.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Image not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Image not found")
     *         )
     *     )
     * )
     */
    public function updateImage(Image $image, array $data)
    {
        try {
            if (isset($data['main'])) {
                $data['main'] = filter_var($data['main'], FILTER_VALIDATE_BOOLEAN);
            }
            $this->validateImageData($data, 'sometimes');
            $this->checkOwnership($image->product, 'Image', 'update');
            if (isset($data['product_id'])) {
                $product = Product::find($data['product_id']);
                $this->checkOwnership($product, 'Image', 'update');
            }$image = $this->imageRepository->update($image, $data);
            $data = [
                'Image' => ImageResource::make($image),
            ];

            $response = ResponseHelper::jsonResponse($data, 'Image updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/images/{id}",
     *     summary="Delete a Image",
     *     tags={"Images"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Image ID you want to delete it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Image deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Image deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this Image.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Image not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Image not found")
     *         )
     *     )
     * )
     */
    public function deleteImage(Image $image)
    {
        try {
            $this->checkOwnership($image->product, 'Image', 'delete');
            $this->imageRepository->delete($image);
            $response = ResponseHelper::jsonResponse([], 'Image deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validateImageData(array $data, $rule = 'required')
    {
        $validator = Validator::make($data, [
            'image' => "$rule|image|max:5120",
            'main' => "$rule|nullable|boolean",
            'product_id' => "$rule|exists:products,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
