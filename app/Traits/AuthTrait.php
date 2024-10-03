<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Exceptions\HttpResponseException;

trait AuthTrait
{
    public function checkOwnership($model, $modelType, $action, $relation = null, $relationName = null)
    {
        if ($relation && $model->$relation()->exists()) {
            throw new HttpResponseException(response()->json([
                'message' => "You are not authorized to {$action} this {$modelType}. It has associated {$relationName}.",
                'successful' => false,
            ], 403));
        }

        if ($model->user_id !== auth()->id()) {
            throw new HttpResponseException(response()->json([
                'message' => "You are not authorized to {$action} this {$modelType}.",
                'successful' => false,
            ], 403));
        }
    }

    public function checkProduct($model, $modelType, $action, $relation = null, $relationName = null)
    {
        $userHasProductInCategory = Product::where('category_id', $model->id)
            ->where('user_id', auth()->id())
            ->exists();

        if (! $userHasProductInCategory) {
            throw new HttpResponseException(response()->json([
                'message' => "You are not authorized to {$action} this {$modelType}. You must have products in this category.",
                'successful' => false,
            ], 403));
        }
    }

    public function checkReviewOwnership($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if ($review->user_id !== auth()->id()) {
            throw new HttpResponseException(response()->json([
                'message' => 'You are not authorized to comment on this review. It does not belong to you.',
                'successful' => false,
            ], 403));
        }
    }
}
