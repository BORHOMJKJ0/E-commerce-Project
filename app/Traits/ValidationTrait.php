<?php

namespace App\Traits;

use App\Helpers\ResponseHelper;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{
    public function validateRequest($request, $rule): ?JsonResponse
    {
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return null;
    }

    public function checkDate(array $data, string $name, string $condition)
    {
        $dateTime = Carbon::parse($data[$name]);

        if ($condition === 'future' && $dateTime->isPast()) {
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                "The {$name} must be in the future.",
                400, false));
        }
    }

    public function checkOfferEndDate($expiryDate, $end_date, ?string $offer_end = null)
    {
        $offerEndDate = Carbon::parse($end_date);
        $offerEnd = $offer_end ? Carbon::parse($offer_end) : null;

        if ($offerEndDate->gt($expiryDate) || ($offerEnd && $offerEnd->gt($expiryDate))) {
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                "The offer end date must be before or equal to the product expiry date {$expiryDate} .",
                400, false));
        }

        return true;
    }

    public function checkOfferDates(Offer $offer, string $action)
    {
        $endDate = Carbon::parse($offer['end_date']);
        $startDate = Carbon::parse($offer['start_date']);
        if ($startDate->lte(now()) && $endDate->gte(now())) {
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                "Cannot {$action} an offer that started and has not yet ended. It will be deleted automatically after it ends.",
                400, false));
        }

    }

    protected function checkDiscount($newOfferDiscount, $start, $existingOffers)
    {
        $previousOffer = null;
        $nextOffer = null;

        foreach ($existingOffers as $offer) {
            if ($offer->start_date < $start) {
                $previousOffer = $offer;
            } elseif ($offer->start_date > $start && ! $nextOffer) {
                $nextOffer = $offer;
                break;
            }
        }

        if ($previousOffer && $nextOffer) {
            if ($newOfferDiscount <= $previousOffer->discount_percentage ||
                $newOfferDiscount >= $nextOffer->discount_percentage) {
                throw new HttpResponseException(ResponseHelper::jsonResponse(
                    [],
                    "The discount percentage must be between the previous offer ({$previousOffer->discount_percentage}%) and the next offer ({$nextOffer->discount_percentage}%).",
                    400,
                    false
                ));
            }
        } elseif ($previousOffer) {
            if ($newOfferDiscount <= $previousOffer->discount_percentage) {
                throw new HttpResponseException(ResponseHelper::jsonResponse(
                    [],
                    "The discount percentage must be greater than the previous offer ({$previousOffer->discount_percentage}%).",
                    400,
                    false
                ));
            }
        } elseif ($nextOffer) {
            if ($newOfferDiscount >= $nextOffer->discount_percentage) {
                throw new HttpResponseException(ResponseHelper::jsonResponse(
                    [],
                    "The discount percentage must be less than the next offer ({$nextOffer->discount_percentage}%).",
                    400,
                    false
                ));
            }
        }
    }

    protected function checkOfferOverlap($warehouseId, $startDate, $endDate, $ignoreOfferId = null)
    {
        $existingOffers = Offer::where('warehouse_id', $warehouseId)
            ->when($ignoreOfferId, function ($query) use ($ignoreOfferId) {
                return $query->where('id', '!=', $ignoreOfferId);
            })
            ->get();

        $overlappingOffers = collect();
        $allExistingOffers = collect();

        foreach ($existingOffers as $offer) {
            $allExistingOffers->push([
                'id' => $offer->id,
                'discount_percentage' => $offer->discount_percentage,
                'start_date' => $offer->start_date->format('Y-n-j'),
                'end_date' => $offer->end_date->format('Y-n-j'),
            ]);

            if ($this->isOverlapping($startDate, $endDate, $offer->start_date, $offer->end_date, 'Full')) {
                $overlappingOffers->push([
                    'id' => $offer->id,
                    'discount_percentage' => $offer->discount_percentage,
                    'start_date' => $offer->start_date->format('Y-n-j'),
                    'end_date' => $offer->end_date->format('Y-n-j'),
                ]);
            } elseif ($this->isOverlapping($startDate, $endDate, $offer->start_date, $offer->end_date, 'Partial')) {
                $overlappingOffers->push([
                    'id' => $offer->id,
                    'discount_percentage' => $offer->discount_percentage,
                    'start_date' => $offer->start_date->format('Y-n-j'),
                    'end_date' => $offer->end_date->format('Y-n-j'),
                ]);
            }
        }

        if ($overlappingOffers->isNotEmpty()) {
            throw new HttpResponseException(ResponseHelper::jsonResponse(
                [
                    'overlapping_offers' => $overlappingOffers,
                    'existing_offers' => $allExistingOffers,
                ],
                'The offer dates conflict with existing offers.',
                400,
                false
            ));
        }

        return true;
    }

    private function isOverlapping($start1, $end1, $start2, $end2, $type)
    {
        if ($type == 'Full') {
            return $start1 >= $start2 && $end1 <= $end2;
        } else {
            return $start1 < $end2 && $end1 > $start2;
        }
    }
}
