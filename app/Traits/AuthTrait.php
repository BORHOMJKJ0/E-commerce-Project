<?php

namespace App\Traits;

use App\Helpers\ResponseHelper;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Exceptions\HttpResponseException;

trait AuthTrait
{
    public function checkOwnership($model, $modelType, $action, $relation = null, $relationName = null)
    {
        if ($relation && $model->$relation()->exists()) {
            $unauthorizedProducts = $model->$relation()->where('user_id', '!=', auth()->id())->exists();

            if ($unauthorizedProducts) {
                throw new HttpResponseException(ResponseHelper::jsonResponse([],
                    "You are not authorized to {$action} this {$modelType}. It has associated {$relationName}.",
                    403, false));
            }
        } elseif ($model->user_id !== auth()->id()) {
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                "You are not authorized to {$action} this {$modelType}.",
                403, false));
        }
    }

    public function checkProduct($model, $modelType, $action)
    {
        if ($action == 'Category') {
            $userHasProductInCategory = Product::where('category_id', $model->id)
                ->where('user_id', auth()->id())
                ->exists();

            if (! $userHasProductInCategory) {
                throw new HttpResponseException(ResponseHelper::jsonResponse([],
                    "You are not authorized to {$action} this {$modelType}. You must have products in this category.",
                    403, false));
            }
        } else {
            if ($model->warehouses()->exists()) {
                $activeWarehouse = $model->warehouses()->where('expiry_date', '>', now())->first();

                if ($activeWarehouse) {
                    throw new HttpResponseException(ResponseHelper::jsonResponse([],
                        "Cannot {$action} this product because it has Valid quantities, it deleted automatically after it's expiry_date ends.",
                        403, false,
                    ));
                }
            }
        }
    }

    public function checkComment($model, $modelType, $action)
    {
        if ($model->review->user_id !== auth()->id()) {
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                "You are not authorized to {$action} this {$modelType}.",
                403, false));
        }
    }

    public function checkReviewOwnership($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if ($review->user_id !== auth()->id()) {
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                'You are not authorized to comment on this review. It does not belong to you.',
                403, false));
        }
    }
}
