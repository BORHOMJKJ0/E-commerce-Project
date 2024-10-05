<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchProductRequest;
use App\Services\ProductService;
use App\Services\SearchProductService;
use Illuminate\Http\Request;

class SearchProductController extends Controller
{
    private $SearchProductService;

    public function __construct(SearchProductService $SearchProductService)
    {
        $this->SearchProductService = $SearchProductService;
    }

    public function search(SearchProductRequest $request)
    {
        $filters = $request->validated();

        return  $this->SearchProductService->searchProducts($filters);
    }
}
