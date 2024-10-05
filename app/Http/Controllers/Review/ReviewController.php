<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
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
        return $this->reviewService->getAllReviews($request);
    }

    public function MyReviews(Request $request): JsonResponse
    {
        return $this->reviewService->getMyReviews($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->reviewService->createReview($request->all());
    }

    public function show(Review $review): JsonResponse
    {
        return $this->reviewService->getReviewById($review);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->reviewService->getReviewsOrderedBy($column, $direction, $request);
    }

    public function MyReviewsOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->reviewService->getMyReviewsOrderedBy($column, $direction, $request);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        return $this->reviewService->updateReview($review, $request->all());
    }

    public function destroy(Review $review): JsonResponse
    {
        return $this->reviewService->deleteReview($review);
    }
}
