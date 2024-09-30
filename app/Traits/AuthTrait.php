<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;

trait AuthTrait
{
    public function checkOwnership($model, $modelType, $action, $relation = null, $relationName = null)
    {
        if ($relation && $model->$relation()->exists()) {
            throw new HttpResponseException(response()->json([
                'Message' => "You are not authorized to {$action} this {$modelType}. It has associated {$relationName}.",
                'Success' => false,
            ], 403));
        }

        if ($model->user_id !== auth()->id()) {
            throw new HttpResponseException(response()->json([
                'Message' => "You are not authorized to {$action} this {$modelType}.",
                'Success' => false,
            ], 403));
        }
        if ($relation === 'products') {
            $userHasProductInCategory = Product::where('category_id', $model->id)
                ->where('user_id', auth()->id())
                ->exists();

            if (! $userHasProductInCategory) {
                throw new HttpResponseException(response()->json([
                    'Message' => "You are not authorized to {$action} this {$modelType}. You must have products in this category.",
                    'Success' => false,
                ], 403));
            }
        }
    }
}
