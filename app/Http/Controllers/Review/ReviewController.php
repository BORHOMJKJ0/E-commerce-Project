<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->middleware('auth:api');
        $this->reviewService = $reviewService;
    }

    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $this->reviewService->getAllReviews($page, $items);
        $hasMorePages = $reviews->hasMorePages();

        $data = [
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'All reviews retrieved successfully');
    }

    public function MyReviews(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $this->reviewService->getMyReviews($page, $items);
        $hasMorePages = $reviews->hasMorePages();

        $data = [
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'All reviews retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $review = $this->reviewService->createReview($request->all());

        $data = ['review' => ReviewResource::make($review)];
        return ResponseHelper::jsonResponse($data, 'Review created successfully!', 201);
    }

    public function show(Review $review): JsonResponse
    {
        $review = $this->reviewService->getReviewById($review);

        $data = ['review' => ReviewResource::make($review)];
        return ResponseHelper::jsonResponse($data, 'Review retrieved successfully!');
    }

    public function ReviewOrderBy($column, $direction, Request $request, bool $isMyReviews = false)
    {
        $validColumns = ['rating', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $isMyReviews
            ? $this->reviewService->getMyReviewsOrderedBy($column, $direction, $page, $items)
            : $this->reviewService->getReviewsOrderedBy($column, $direction, $page, $items);

        $hasMorePages = $reviews->hasMorePages();

        $data = [
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages
        ];
        return ResponseHelper::jsonResponse($data, 'Reviews ordered successfully');
    }
    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->ReviewOrderBy($column, $direction, $request);
    }

    public function MyReviewsOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->ReviewOrderBy($column, $direction, $request, true);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        try {

            $review = $this->reviewService->updateReview($review, $request->all());

            return response()->json([
                'message' => 'Review updated successfully!',
                'review' => ReviewResource::make($review),
                'successful' => true,
            ], 200);
        } catch (HttpResponseException $e) {
            return response()->json($e->getResponse()->getData(), 403);
        }
    }

    public function destroy(Review $review): JsonResponse
    {
        try {

            $this->reviewService->deleteReview($review);

            return response()->json(['message' => 'Review deleted successfully!', 'successful' => true], 200);
        } catch (HttpResponseException $e) {
            return response()->json($e->getResponse()->getData(), 403);
        }
    }
}
