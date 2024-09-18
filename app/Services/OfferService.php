<?php

namespace App\Services;

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
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/OfferResource")
     *         )
     *     )
     * )
     */
    public function getAllOffers()
    {
        return $this->offerRepository->getAll();
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
    public function getOfferById($id)
    {
        return $this->offerRepository->findById($id);
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
     *      @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *
     *                 @OA\Property(property="name", type="string", example="fruits"),
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
     *         @OA\Schema(type="string", enum={"name", "created_at", "updated_at"})
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
     *             @OA\Property(property="error", type="string", example="Invalid column or direction")
     *         )
     *     )
     * )
     */
    public function getOffersOrderedBy($column, $direction)
    {
        return $this->offerRepository->orderBy($column, $direction);
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
     *         name="name",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="Vegetables")
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
    public function updateOffer($id, array $data)
    {
        $offer = $this->offerRepository->findById($id);

        $this->validateOfferData($data, 'sometimes');

        return $this->offerRepository->update($id, $data);
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
    public function deleteOffer($id)
    {
        return $this->offerRepository->delete($id);
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
