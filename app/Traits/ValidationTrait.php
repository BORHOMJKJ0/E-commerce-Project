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

        if ($condition === 'now' && $dateTime->lt(now()->startOfDay())) {
            throw new HttpResponseException(ResponseHelper::jsonResponse([],
                "The {$name} must be greater than or equal to now.",
                400, false));
        }
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
}
