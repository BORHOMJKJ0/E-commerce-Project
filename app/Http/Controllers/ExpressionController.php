<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpressionRequest;
use App\Http\Requests\UpdateExpressionRequest;
use App\Models\Product;
use App\Services\ExpressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpressionController extends Controller
{
    private $expressionService;

    public function __construct(ExpressionService $expressionService)
    {
        $this->middleware('auth:api');
        $this->expressionService = $expressionService;
    }

    public function index()
    {
        return $this->expressionService->index();
    }

    public function create(ExpressionRequest $request): JsonResponse
    {
        return $this->expressionService->create($request);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->expressionService->show($product);
    }

    public function update(UpdateExpressionRequest $request, Product $product): JsonResponse
    {
        return $this->expressionService->update($request, $product);
    }

    // public function delete(Product $product): JsonResponse
    // {
    //     //
    // }
}
