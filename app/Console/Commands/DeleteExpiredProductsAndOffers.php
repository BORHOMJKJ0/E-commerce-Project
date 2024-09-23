<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredProductsAndOffers extends Command
{
    protected $signature = 'delete:expired-products-and-offers';

    protected $description = 'Delete expired products and offers';

    public function handle()
    {
        $warehousesGroupedByProduct = Warehouse::all()->groupBy('product_id');

        foreach ($warehousesGroupedByProduct as $productId => $warehouses) {
            $totalAmount = $warehouses->sum('amount');

            if ($totalAmount == 0) {
                foreach ($warehouses as $warehouse) {
                    if (is_null($warehouse->settlement_date)) {
                        $warehouse->settlement_date = now();
                        $warehouse->save();
                        $this->info("Updated settlement_date for warehouse with ID: {$warehouse->id} because total product amount is 0.");
                    }

                    if (! is_null($warehouse->settlement_date)) {
                        $warehouse->delete();
                        $this->info("Deleted warehouse with ID: {$warehouse->id} after updating settlement_date.");
                    }
                }

                $product = Product::find($productId);
                if ($product) {
                    $product->delete();
                    $this->info("Deleted product with ID: {$productId} because total product amount is 0.");
                }
            }

            foreach ($warehouses as $warehouse) {
                if ($warehouse->expiry_date < now()) {
                    if ($warehouse->product) {
                        $warehouse->product->delete();
                        $this->info("Deleted product with ID: {$warehouse->product_id} due to expiry.");
                    }
                }

            }
        }

        $warehouseProductIds = Warehouse::pluck('product_id')->toArray();
        $expiredProducts = Product::whereNotIn('id', $warehouseProductIds)
            ->where('created_at', '<', Carbon::now()->subHour())
            ->get();

        foreach ($expiredProducts as $product) {
            $product->delete();
            $this->info("Deleted product with ID: {$product->id} created more than 1 hour ago.");
        }

        $expiredOffers = Offer::where('end_date', '<', Carbon::now())->get();
        foreach ($expiredOffers as $offer) {
            $offer->delete();
            $this->info("Deleted offer for product: {$offer->product->name}");
        }

        return 0;
    }
}
