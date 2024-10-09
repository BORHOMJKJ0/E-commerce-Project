<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = collect($errors)->flatten()->toArray();

            $firstError = $errorMessages[0];
            $additionalErrorsCount = count($errorMessages) - 1;

            // $summaryMessage = $additionalErrorsCount > 0
            //     ? "$firstError (and $additionalErrorsCount more error".($additionalErrorsCount > 1 ? 's' : '').')'
            //     : $firstError;

            return ResponseHelper::jsonResponse($errors, 'Validation Failed', 400, false);
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $modelName = $exception->getModel();

            switch ($modelName) {
                case 'App\\Models\\Product':
                    return ResponseHelper::jsonResponse([], 'Product not found', 400, false);
                case 'App\\Models\\Category':
                    return ResponseHelper::jsonResponse([], 'Category not found', 400, false);
                case 'App\\Models\\Offer':
                    return ResponseHelper::jsonResponse([], 'Offer not found', 400, false);
                case 'App\\Models\\Warehouse':
                    return ResponseHelper::jsonResponse([], 'Warehouse not found', 400, false);
                case 'App\\Models\\Review':
                    return ResponseHelper::jsonResponse([], 'Review not found', 400, false);
                case 'App\\Models\\Comment':
                    return ResponseHelper::jsonResponse([], 'Comment not found', 400, false);
                case 'App\\Models\\User':
                    return ResponseHelper::jsonResponse([], 'User not found', 400, false);
                default:
                    return ResponseHelper::jsonResponse([], 'Resource not found', 400, false);
            }
        }

        if ($exception instanceof HttpResponseException) {
            return ResponseHelper::jsonResponse([], $exception->getMessage(), $exception->getCode(), false);
        }

        return parent::render($request, $exception);
    }
}
