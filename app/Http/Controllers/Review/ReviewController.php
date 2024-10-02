<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);
    }

    public function MyReviews(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $this->reviewService->getMyReviews($page, $items);
        $hasMorePages = $reviews->hasMorePages();

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $review = $this->reviewService->createReview($request->all());

        return response()->json([
            'message' => 'Review created successfully!',
            'review' => ReviewResource::make($review),
            'successful' => true,
        ], 201);
    }

    public function show(Review $review): JsonResponse
    {
        $review = $this->reviewService->getReviewById($review);

        return response()->json([
            'message' => 'Review performed successfully!',
            'review' => ReviewResource::make($review),
            'successful' => true,
        ], 200);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['rating', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $this->reviewService->getReviewsOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $reviews->hasMorePages();

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);

    }

    public function MyReviewsOrderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['rating', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $reviews = $this->reviewService->getMyReviewsOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $reviews->hasMorePages();

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);

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
