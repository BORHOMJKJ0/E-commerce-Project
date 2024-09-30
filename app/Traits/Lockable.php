<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

trait Lockable
{
    protected function lockForCreate(callable $callback)
    {
        return DB::transaction(function () use ($callback) {
            return $callback();
        });
    }

    protected function lockForUpdate($modelClass, $id, callable $callback)
    {
        return DB::transaction(function () use ($modelClass, $id, $callback) {
            $model = $this->lockAndRetrieve($modelClass, $id);

            return $callback($model);
        });
    }

    protected function lockForDelete($modelClass, $id, callable $callback)
    {
        return DB::transaction(function () use ($modelClass, $id, $callback) {
            $model = $this->lockAndRetrieve($modelClass, $id);

            return $callback($model);
        });
    }

    protected function lockAndRetrieve($modelClass, $id)
    {
        $model = $modelClass::where('id', $id)->lockForUpdate()->first();

        if (! $model) {
            throw new HttpResponseException(response()->json([
                'message' => 'Model not found or could not be locked.',
                'successful' => false,
            ], 404));
        }

        return $model;
    }
}
