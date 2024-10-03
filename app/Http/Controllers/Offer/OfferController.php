<?php

namespace App\Http\Controllers\Offer;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Http\Exceptions\HttpResponseException;
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

        $data = [
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonRespones($data, message: 'Offers retrieved successfully');
    }

    public function MyOffers(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerService->getMyOffers($page, $items);
        $hasMorePages = $offers->hasMorePages();

        $data = [
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonRespones($data, message: 'Offers retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {

        try {
            $offer = $this->offerService->createOffer($request->all());

            $data = ['offer' => OfferResource::make($offer)];

            return ResponseHelper::jsonRespones($data, 'Offer created successfully!', 201);
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }

    public function show(Offer $offer): JsonResponse
    {
        $offer = $this->offerService->getOfferById($offer);

        $data = ['offer' => OfferResource::make($offer)];

        return ResponseHelper::jsonRespones($data, 'Offer performed successfully!');
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['discount_percentage', 'start_date', 'end_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerService->getOffersOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $offers->hasMorePages();

        $data = [
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonRespones($data, 'Offers ordered successfully!');
    }

    public function MyOffersOrderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['discount_percentage', 'start_date', 'end_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerService->getMyOffersOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $offers->hasMorePages();

        $data = [
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonRespones($data, 'Offers ordered successfully!');
    }

    public function update(Request $request, Offer $offer): JsonResponse
    {
        try {

            $offer = $this->offerService->updateOffer($offer, $request->all());

            $data = ['offer' => OfferResource::make($offer)];

            return ResponseHelper::jsonRespones($data, 'Offer updated successfully!');
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }

    public function destroy(Offer $offer): JsonResponse
    {
        try {

            $this->offerService->deleteOffer($offer);

            return ResponseHelper::jsonRespones([], 'Offer deleted successfully!');
        } catch (HttpResponseException $e) {
            $message = $e->getResponse()->getData();

            return ResponseHelper::jsonRespones([], $message, 403, false);
        }
    }
}
