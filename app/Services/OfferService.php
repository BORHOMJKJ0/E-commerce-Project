<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Models\Product;
use App\Repositories\OfferRepository;
use App\Traits\AuthTrait;
use App\Traits\ValidationTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OfferService
{
    use AuthTrait,ValidationTrait;

    protected $offerRepository;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Enter JWT Bearer token in the format 'Bearer {token}'"
     * )
     */
    /**
     * @OA\Get(
     *     path="/api/offers",
     *     summary="Get all offers",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/OfferResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function getAllOffers(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerRepository->getAll($items, $page);
        $hasMorePages = $offers->hasMorePages();

        $data = [
            'Offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Offers retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/offers/my",
     *     summary="Get My offers",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/OfferResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function getMyOffers(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerRepository->getMy($items, $page);
        $hasMorePages = $offers->hasMorePages();

        $data = [
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Offers retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/offers/{id}",
     *     summary="Get a offer by ID",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Offer ID you want to show it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/OfferResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Offer not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Offer not found")
     *         )
     *     )
     * )
     */
    public function getOfferById(Offer $offer)
    {
        $data = ['offer' => OfferResource::make($offer)];

        return ResponseHelper::jsonResponse($data, 'Offer performed successfully!');
    }

    /**
     * @OA\Post(
     *     path="/api/offers",
     *     summary="Create a offer",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"discount_percentage", "start_date", "end_date", "product_id"},
     *
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example="15.50",description="Discount_percentage of the offer",),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2024-10-01",description="Offer Start Date",),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2024-12-31",description="Offer End Date",),
     *                 @OA\Property(property="product_id", type="integer", example=1,description="Product ID you want to add offer to it",),
     *             )
     *         )
     *     ),
     *
     *    @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Offer created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/OfferResource")
     *     ),
     *
     *      @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to create this Offer.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     * )
     */
    public function createOffer(array $data)
    {
        try {
            $this->validateOfferData($data);
            $product = Product::find($data['product_id']);
            $this->checkOwnership($product, 'Offer', 'create');
            $this->checkDate($data, 'start_date', 'now');
            $this->checkOfferEndDate($data['product_id'], $data['end_date']);
            $this->checkOfferOverlap($product->id, $data['start_date'], $data['end_date']);
            $offer = $this->offerRepository->create($data);
            $data = ['offer' => OfferResource::make($offer)];

            $response = ResponseHelper::jsonResponse($data, 'Offer created successfully!', 201);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/offers/order/{column}/{direction}",
     *     summary="Order offers by a specific column",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *     description="Column you want to order the offers by it",
     *
     *         @OA\Schema(type="string", enum={"discount_percentage","start_date","end_date", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *     description="Dircetion of ordering",
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page ",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/OfferResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction or parameters")
     *         )
     *     )
     * )
     */
    public function getOffersOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['discount_percentage', 'start_date', 'end_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $offers->hasMorePages();

        $data = [
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Offers ordered successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/offers/my/order/{column}/{direction}",
     *     summary="Order My offers by a specific column",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *         description="Column you want to order the offers by it",
     *
     *         @OA\Schema(type="string", enum={"discount_percentage","start_date","end_date", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *     description="Dircetion of ordering",
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page ",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/OfferResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction or parameters")
     *         )
     *     )
     * )
     */
    public function getMyOffersOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['discount_percentage', 'start_date', 'end_date', 'created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $offers = $this->offerRepository->orderMyBy($column, $direction, $page, $items);
        $hasMorePages = $offers->hasMorePages();

        $data = [
            'offers' => OfferResource::collection($offers),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Offers ordered successfully!');
    }

    /**
     * @OA\Put(
     *     path="/api/offers/{id}",
     *     summary="Update an offer",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Offer ID you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="discount_percentage",
     *         in="query",
     *         required=false,
     *     description="Discount Percentage of the offer",
     *
     *         @OA\Schema(type="number", format="float", example=20.5)
     *     ),
     *
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *     description="Start Date of the offer",
     *
     *         @OA\Schema(type="string", format="date", example="2024-10-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *     description="End Date of the offer",
     *
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=false,
     *     description="Product ID of the offer",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Offer updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="discount_percentage", type="number", format="float", example=80.00),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-09-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
     *             @OA\Property(property="product", type="object",
     *                 @OA\Property(property="name", type="string", example="Iphone 15"),
     *                 @OA\Property(property="price", type="number", format="float", example=499.99),
     *                 @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design"),
     *                 @OA\Property(property="category", type="string", example="Smartphone"),
     *                 @OA\Property(property="user", type="string", example="Hasan Zaeter")
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *
     *      @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to update this Offer.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Offer not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Offer not found")
     *         )
     *     )
     * )
     */
    public function updateOffer(Offer $offer, array $data)
    {
        try {
            $this->validateOfferData($data, 'sometimes');
            $product = $offer->product;
            $this->checkOwnership($product, 'Offer', 'update');
            $startDate = $data['start_date'] ?? $offer->start_date;
            $endDate = $data['end_date'] ?? $offer->end_date;
            $productId = $data['product_id'] ?? $offer->product_id;
            $this->checkOfferDates($offer, 'update');
            $this->checkOfferEndDate($data['product_id'] ?? $offer['product_id'], $offer->end_date, $data['end_date']);
            $this->checkOfferOverlap($productId, $startDate, $endDate, $offer->id);
            $offer = $this->offerRepository->update($offer, $data);
            $data = ['offer' => OfferResource::make($offer)];

            $response = ResponseHelper::jsonResponse($data, 'Offer updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/offers/{id}",
     *     summary="Delete a offer",
     *      tags={"Offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Offer ID you want to delete it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Offer deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Offer deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this Offer.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Offer not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Offer not found")
     *         )
     *     )
     * )
     */
    public function deleteOffer(Offer $offer)
    {
        try {

            $product = $offer->product;
            $this->checkOwnership($product, 'Offer', 'delete');
            $this->checkOfferDates($offer, 'delete');
            $this->offerRepository->delete($offer);
            $response = ResponseHelper::jsonResponse([], 'Offer deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validateOfferData(array $data, $rule = 'required')
    {
        $validator = Validator::make($data, [
            'discount_percentage' => "$rule|numeric|between:0,100",
            'start_date' => "$rule|date",
            'end_date' => "$rule|date|after_or_equal:start_date",
            'product_id' => "$rule|exists:products,id",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    protected function checkOfferOverlap($productId, $startDate, $endDate, $ignoreOfferId = null)
    {
        $existingOffers = Offer::where('product_id', $productId)
            ->when($ignoreOfferId, function ($query, $ignoreOfferId) {
                return $query->where('id', '!=', $ignoreOfferId);
            })
            ->get();

        foreach ($existingOffers as $offer) {
            if (
                ($startDate >= $offer->start_date && $startDate <= $offer->end_date) ||
                ($endDate >= $offer->start_date && $endDate <= $offer->end_date) ||
                ($startDate <= $offer->start_date && $endDate >= $offer->end_date)
            ) {
                throw new HttpResponseException(ResponseHelper::jsonResponse([],
                    "This offer overlaps with an existing offer from {$offer->start_date} to {$offer->end_date} for this product: {$offer->product->name}.",
                    403, false));
            }
        }
    }
}
