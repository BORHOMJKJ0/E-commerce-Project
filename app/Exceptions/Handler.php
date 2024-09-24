<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = collect($errors)->flatten()->toArray();

            $firstError = $errorMessages[0];
            $additionalErrorsCount = count($errorMessages) - 1;

            $summaryMessage = $additionalErrorsCount > 0
                ? "$firstError (and $additionalErrorsCount more error".($additionalErrorsCount > 1 ? 's' : '').')'
                : $firstError;

            return response()->json([
                'message' => $summaryMessage,
                'errors' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $modelName = $exception->getModel();

            switch ($modelName) {
                case 'App\\Models\\Product':
                    return response()->json([
                        'message' => 'Product not found',
                    ], 404);
                case 'App\\Models\\Category':
                    return response()->json([
                        'message' => 'Category not found',
                    ], 404);
                case 'App\\Models\\Offer':
                    return response()->json([
                        'message' => 'Offer not found',
                    ], 404);
                case 'App\\Models\\Warehouse':
                    return response()->json([
                        'message' => 'Warehouse not found',
                    ], 404);
                default:
                    return response()->json([
                        'message' => 'Resource not found',
                    ], 404);
            }
        }

        if ($exception instanceof UnauthorizedActionException) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        return parent::render($request, $exception);
    }
}
