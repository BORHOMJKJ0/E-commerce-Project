<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class SearchProductService
{
    public function searchProducts(array $filters)
    {
        $query = Product::query();

        $this->applyFilters($query, $filters);

        return $query;
    }
    private function applyFilters(Builder $query, array $filters)
    {
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
            $query->whereBetween('price', [$filters['min_price'], $filters['max_price']]);
        }

        if (!empty($filters['expiry_date'])) {
            $query->where('expiry_date', '<=', $filters['expiry_date']);
        }
    }
}
