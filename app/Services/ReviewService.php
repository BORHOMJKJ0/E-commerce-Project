<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Repositories\ReviewRepository;
use App\Traits\AuthTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    use AuthTrait;

    protected $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
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
     *     path="/api/reviews",
     *     summary="Get all reviews",
     *     tags={"Reviews"},
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
     *             @OA\Items(ref="#/components/schemas/ReviewResource")
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
    public function getAllReviews(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $reviews = $this->reviewRepository->getAll($items, $page);
        $hasMorePages = $reviews->hasMorePages();

        $data = [
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Reviews retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/my",
     *     summary="Get My reviews",
     *     tags={"Reviews"},
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
     *             @OA\Items(ref="#/components/schemas/ReviewResource")
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
    public function getMyReviews(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $reviews = $this->reviewRepository->getMy($items, $page);
        $hasMorePages = $reviews->hasMorePages();

        $data = [
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Reviews retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/{id}",
     *     summary="Get a review by ID",
     *     tags={"Reviews"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Review ID you want to show it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ReviewResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Review not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Review not found")
     *         )
     *     )
     * )
     */
    public function getReviewById(Review $review)
    {
        $data = ['review' => ReviewResource::make($review)];

        return ResponseHelper::jsonResponse($data, 'Review created successfully!', 201);

    }

    /**
     * @OA\Post(
     *     path="/api/reviews",
     *     summary="Create a review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *      @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"rating","product_id"},
     *
     *               @OA\Property(property="rating", type="number", format="float", example="4.5", description="The rating of the product, a number between 0 and 5"),
     *                @OA\Property(property="product_id", type="integer", example=1,description="Product ID you want to add review to it"),
     *             )
     *         )
     *     ),
     *
     *    @OA\Header(
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
     *         description="Review created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="rating", type="number",format="float", example="4"),
     *             @OA\Property(property="product_id", type="integer", example=1),
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
    public function createReview(array $data)
    {
        $data['user_id'] = auth()->id();
        $this->validateReviewData($data);
        $review = $this->reviewRepository->create($data);
        $data = ['review' => ReviewResource::make($review)];

        return ResponseHelper::jsonResponse($data, 'Review retrieved successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/order/{column}/{direction}",
     *     summary="Order reviews by a specific column",
     *     tags={"Reviews"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *         description="Column you want to order the reviews by it",
     *
     *         @OA\Schema(type="string", enum={"rating", "created_at", "updated_at"})
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
     *             @OA\Items(ref="#/components/schemas/ReviewResource")
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
    public function getReviewsOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['rating', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $this->reviewRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $reviews->hasMorePages();

        $data = [
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Reviews ordered successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/my/order/{column}/{direction}",
     *     summary="Order My reviews by a specific column",
     *     tags={"Reviews"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *        description="Column you want to order the reviews by it",
     *
     *         @OA\Schema(type="string", enum={"rating", "created_at", "updated_at"})
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
     *             @OA\Items(ref="#/components/schemas/ReviewResource")
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
    public function getMyReviewsOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['rating', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $this->reviewRepository->orderMyBy($column, $direction, $page, $items);
        $hasMorePages = $reviews->hasMorePages();

        $data = [
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Reviews ordered successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/reviews/{id}",
     *     summary="Update a review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="review ID you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         required=false,
     *     description="your rating in this product",
     *
     *         @OA\Schema(type="number",format="float" ,example="3")
     *     ),
     *
     *      @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=false,
     *     description="The product you want to add rating to it",
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
     *         description="Review updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="rating", type="number",format="float", example="2"),
     *             @OA\Property(property="product_id", type="integer", example=1),
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
     *     @OA\Response(
     *         response=404,
     *         description="Review not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Review not found")
     *         )
     *     )
     * )
     */
    public function updateReview(Review $review, array $data)
    {
        try {
            $this->validateReviewData($data, 'sometimes');
            $this->checkOwnership($review, 'Review', 'update');
            $review = $this->reviewRepository->update($review, $data);
            $data = ['review' => ReviewResource::make($review)];
            $response = ResponseHelper::jsonResponse($data, 'Review updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/reviews/{id}",
     *     summary="Delete a review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Review ID you want to delete it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Review deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Review deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this Review.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Review not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Rwview not found")
     *         )
     *     )
     * )
     */
    public function deleteReview(Review $review)
    {
        try {
            $this->checkOwnership($review, 'Review', 'delete');
            $this->reviewRepository->delete($review);
            $response = ResponseHelper::jsonResponse([], 'Review deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validateReviewData(array $data, $rule = 'required')
    {
        $validator = Validator::make($data, [
            'rating' => "$rule|numeric|min:0|max:5",
            'product_id' => "$rule|exists:products,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
