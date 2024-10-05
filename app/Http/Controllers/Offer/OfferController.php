<?php

namespace App\Http\Controllers\Offer;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->middleware('auth:api');
        $this->offerService = $offerService;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->offerService->getAllOffers($request);
    }

    public function MyOffers(Request $request): JsonResponse
    {
        return $this->offerService->getMyOffers($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->offerService->createOffer($request->all());
    }

    public function show(Offer $offer): JsonResponse
    {
        return $this->offerService->getOfferById($offer);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->offerService->getOffersOrderedBy($column, $direction, $request);
    }

    public function MyOffersOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->offerService->getMyOffersOrderedBy($column, $direction, $request);
    }

    public function update(Request $request, Offer $offer): JsonResponse
    {
        return $this->offerService->updateOffer($offer, $request->all());
    }

    public function destroy(Offer $offer): JsonResponse
    {
        return $this->offerService->deleteOffer($offer);
    }
}
