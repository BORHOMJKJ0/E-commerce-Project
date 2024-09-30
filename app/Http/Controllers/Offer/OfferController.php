<?php

namespace App\Http\Controllers\Offer;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
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
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerService->getAllOffers($page, $items);
        $hasMorePages = $offers->hasMorePages();

        return response()->json([
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function MyOffers(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerService->getMyOffers($page, $items);
        $hasMorePages = $offers->hasMorePages();

        return response()->json([
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {

        try {
            $offer = $this->offerService->createOffer($request->all());

            return response()->json([
                'message' => 'Offer created successfully!',
                'offer' => OfferResource::make($offer),
            ], 201);
        } catch (HttpResponseException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function show(Offer $offer): JsonResponse
    {
        $offer = $this->offerService->getOfferById($offer);

        return response()->json(OfferResource::make($offer), 200);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['discount_percentage', 'start_date', 'end_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerService->getOffersOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $offers->hasMorePages();

        return response()->json([
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function MyOffersOrderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['discount_percentage', 'start_date', 'end_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerService->getMyOffersOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $offers->hasMorePages();

        return response()->json([
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ], 200);
    }

    public function update(Request $request, Offer $offer): JsonResponse
    {
        try {

            $offer = $this->offerService->updateOffer($offer, $request->all());

            return response()->json([
                'message' => 'Offer updated successfully!',
                'offer' => OfferResource::make($offer),
            ], 200);
        } catch (HttpResponseException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function destroy(Offer $offer): JsonResponse
    {
        try {

            $this->offerService->deleteOffer($offer);

            return response()->json(['message' => 'Offer deleted successfully!'], 200);
        } catch (HttpResponseException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
