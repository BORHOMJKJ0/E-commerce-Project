<?php

namespace App\Services;

use App\Models\Offer;
use App\Repositories\OfferRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OfferService
{
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
     *     tags={"offers"},
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
    public function getAllOffers($page, $items)
    {
        return $this->offerRepository->getAll($items, $page);
    }

    /**
     * @OA\Get(
     *     path="/api/offers/{id}",
     *     summary="Get a offer by ID",
     *     tags={"offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
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
        return $offer;
    }

    /**
     * @OA\Post(
     *     path="/api/offers",
     *     summary="Create a offer",
     *     tags={"offers"},
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
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example="15.50"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2024-10-01"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
     *                 @OA\Property(property="product_id", type="integer", example=1),
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
        $this->validateOfferData($data);

        return $this->offerRepository->create($data);
    }

    /**
     * @OA\Get(
     *     path="/api/offers/order/{column}/{direction}",
     *     summary="Order offers by a specific column",
     *     tags={"offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"discount_percentage","start_date","end_date", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
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
    public function getOffersOrderedBy($column, $direction, $page, $items)
    {
        return $this->offerRepository->orderBy($column, $direction, $page, $items);
    }

    /**
     * @OA\Put(
     *     path="/api/offers/{id}",
     *     summary="Update a offer",
     *     tags={"offers"},
     *      security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="discount_percentage",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="number", format="float", example=20.5)
     *     ),
     *
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-10-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *      @OA\Header(
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
     *         @OA\JsonContent(ref="#/components/schemas/OfferResource")
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
        $this->validateOfferData($data, 'sometimes');

        return $this->offerRepository->update($offer, $data);
    }

    /**
     * @OA\Delete(
     *     path="/api/offers/{id}",
     *     summary="Delete a offer",
     *     tags={"offers"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
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
        return $this->offerRepository->delete($offer);
    }

    protected function validateOfferData(array $data, $rule = 'required')
    {
        $validator = Validator::make($data, [
            'discount_percentage' => "$rule|numeric|between:0,99.99",
            'start_date' => "$rule|date",
            'end_date' => "$rule|date|after_or_equal:start_date",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
