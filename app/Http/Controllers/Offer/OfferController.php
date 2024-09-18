<?php

namespace App\Http\Controllers\Offer;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
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

    public function index(): JsonResponse
    {
        $offers = $this->offerService->getAllOffers();

        return response()->json(OfferResource::collection($offers), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $offer = $this->offerService->createOffer($request->all());

        return response()->json([
            'message' => 'Offer created successfully!',
            'offer' => OfferResource::make($offer),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $offer = $this->offerService->getOfferById($id);

        return response()->json(OfferResource::make($offer), 200);
    }

    public function orderBy($column, $direction): JsonResponse
    {
        $validColumns = ['discount_percentage', 'start_date', 'end_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction'], 400);
        }

        $offers = $this->offerService->getOffersOrderedBy($column, $direction);

        return response()->json(OfferResource::collection($offers), 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $offer = $this->offerService->updateOffer($id, $request->all());

        return response()->json([
            'message' => 'Offer updated successfully!',
            'offer' => OfferResource::make($offer),
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $this->offerService->deleteOffer($id);

        return response()->json(['message' => 'Offer deleted successfully!'], 200);
    }
}
