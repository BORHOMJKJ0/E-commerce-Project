<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    public function definition(): array
    {
        $warehouse = Warehouse::where('amount', '>', 0)
            ->where('expiry_date', '>', now())
            ->inRandomOrder()
            ->firstOrFail();

        $existingOffers = Offer::where('warehouse_id', $warehouse->id)
            ->orderBy('start_date', 'asc')
            ->get();

        $startDate = $this->determineStartDate($existingOffers);
        $endDate = $this->determineEndDate($startDate, $warehouse->expiry_date);

        if ($endDate->lte($startDate)) {
            $endDate = $startDate->copy()->addDay();
        }

        while ($this->hasConflict($startDate, $endDate, $existingOffers)) {
            $startDate = $startDate->addDay();
            $endDate = $this->determineEndDate($startDate, $warehouse->expiry_date);
        }

        $daysToExpiry = $warehouse->expiry_date->diffInDays($endDate);
        $discountPercentage = $this->calculateDiscount($daysToExpiry, $existingOffers, $startDate);

        return [
            'discount_percentage' => $discountPercentage,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'warehouse_id' => $warehouse->id,
        ];
    }

    private function determineStartDate($existingOffers): Carbon
    {
        if ($existingOffers->isNotEmpty()) {
            $lastOfferEndDate = $existingOffers->last()->end_date;

            return Carbon::parse($lastOfferEndDate)->addDay();
        } else {
            return Carbon::now()->addDays(rand(0, 7));
        }
    }

    private function determineEndDate(Carbon $startDate, Carbon $warehouseExpiryDate): Carbon
    {
        $endDate = $startDate->copy()->addDays(1);
        if ($endDate->gt($warehouseExpiryDate)) {
            $endDate = $warehouseExpiryDate;
        }

        return $endDate;
    }

    private function hasConflict(Carbon $startDate, Carbon $endDate, $existingOffers): bool
    {
        foreach ($existingOffers as $offer) {
            if (
                ($startDate->isBetween($offer->start_date, $offer->end_date, true)) ||
                ($endDate->isBetween($offer->start_date, $offer->end_date, true)) ||
                ($offer->start_date->isBetween($startDate, $endDate, true))
            ) {
                return true;
            }
        }

        return false;
    }

    private function calculateDiscount(int $daysToExpiry, $existingOffers, Carbon $startDate): float
    {
        if ($existingOffers->isNotEmpty()) {
            $lastOffer = $existingOffers->last();
            $lastDiscount = $lastOffer->discount_percentage;

            if ($startDate->isAfter($lastOffer->start_date)) {
                // New offer starts after the last offer, should be bigger
                return min($lastDiscount + rand(1, 10), 100.0); // Limit to max 100%
            } else {
                // New offer starts before the last offer, should be smaller
                return max($lastDiscount - rand(1, 10), 0.0); // Limit to min 0%
            }
        }

        // If no existing offers, generate a random discount freely
        if ($daysToExpiry <= 7) {
            return fake()->randomFloat(2, 50, 90);
        } elseif ($daysToExpiry <= 30) {
            return fake()->randomFloat(2, 30, 60);
        } else {
            return fake()->randomFloat(2, 10, 30);
        }
    }
}
