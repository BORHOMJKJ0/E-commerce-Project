<?php

namespace App\Services;

use App\Http\Requests\ContactRequest;
use App\Repositories\ContactRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;

class Contact_InformationService
{
    use ValidationTrait;

    protected $contactRepository;
    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Enter JWT Bearer token in the format 'Bearer {token}'"
     * )
     */
    public function __construct()
    {
        $this->contactRepository = new ContactRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/users/contact/add",
     *     summary="Add contact information",
     *     tags={"Contacts"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"contact_type_id", "link"},
     *
     *             @OA\Property(property="contact_type_id", type="integer", example=1, description="Contact type ID"),
     *             @OA\Property(property="link", type="string", example="your link", description="Contact information value (facebook_account, instagram_account, tiwtter_account)")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Contact information added successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Contact information added successfully"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="error",type="object",example={})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function addContact(ContactRequest $request): JsonResponse
    {

        $validationResponse = $this->validateRequest($request, $request->rules());

        if ($validationResponse) {
            return $validationResponse;
        }

        $data = $request->validated();
        $this->contactRepository->create(auth()->user(), $data);

        return response()->json([
            'message' => 'Contact information added successfully',
            'success' => true,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/contact/show/{user_id}",
     *     summary="Retrieve contact information by user ID",
     *     tags={"Contacts"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer"),
     *         description="The ID of the user to retrieve contact information for"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of contact information",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="contacts", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="contact_type_id", type="integer", example=1),
     *                 @OA\Property(property="link", type="string", example="your link")
     *             ))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Contact information not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Contact information not found")
     *         )
     *     )
     * )
     */
    public function show($user_id): JsonResponse
    {
        $contacts = $this->contactRepository->findByUserId($user_id);
        if (! $contacts) {
            return response()->json(['message' => 'Contact information not found'], 404);
        }

        return response()->json(['contacts' => $contacts]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/contact/remove/{contact_information_id}",
     *     summary="Delete a contact by ID",
     *     tags={"Contacts"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="contact_information_id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer"),
     *         description="The ID of the contact information to delete"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Contact deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Contact deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Contact not found")
     *         )
     *     )
     * )
     */
    public function destroy($contact_information_id): JsonResponse
    {
        $contact = $this->contactRepository->deleteById($contact_information_id);
        if (! $contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        return response()->json(['contact' => $contact, 'message' => 'Contact deleted successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/contact/remove-all",
     *     summary="Delete all contacts for the authenticated user",
     *     tags={"Contacts"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="All contacts deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="All Contacts deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Contacts not found for this user",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Contacts not found for this user")
     *         )
     *     )
     * )
     */
    public function destroyAll()
    {
        $contacts = $this->contactRepository->deleteByUser(auth()->user());
        if (! $contacts) {
            return response()->json(['message' => 'Contacts not found for this user'], 400);
        }

        return response()->json(['contacts' => $contacts, 'message' => 'All Contacts deleted successfully']);
    }
}
