<?php

namespace App\Traits;

use App\Helpers\ResponseHelper;
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
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                'Model not found or could not be locked.',
                404, false));
        }

        return $model;
    }
}
