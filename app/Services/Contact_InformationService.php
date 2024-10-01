<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\UserContactsResource;
use App\Models\Contact_information;
use App\Repositories\ContactRepository;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;

class Contact_InformationService
{
    use ValidationTrait;

    protected $contactRepository;
    protected $userRepository;

    public function __construct(UserRepository $userRepository, ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
        $this->userRepository = $userRepository;
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
        $data = $request->validated();
        $contact = $this->contactRepository->create(auth()->user(), $data);

        return ResponseHelper::jsonRespones(['contact' => $contact], 'Contact information added successfully', 201);
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
            return ResponseHelper::jsonRespones([], 'Contact information not found', 404, false);
        }

        $data = [
            'contacts' => ContactResource::collection($contacts),
        ];

        return ResponseHelper::jsonRespones($data, 'Contact Information retrieved successfully');
    }

    public function update(UpdateContactRequest $request, $user_id, $contact_id)
    {
        $contact_information =  $this->contactRepository->findContactById($contact_id);
        if (!$contact_information) {
            return ResponseHelper::jsonRespones([], 'There is no Contact Information With this id', 404, false);
        }

        $user = $this->userRepository->findById($user_id);

        $this->contactRepository->update($user, $request->validated());

        $data = [
            'user' => new UserContactsResource($user),
        ];
        return ResponseHelper::jsonRespones($data, 'Contact Information updated successfully');
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
            return ResponseHelper::jsonRespones([], 'Contact not found', 404, false);
        }
 
        $data = ['contact' => new ContactResource($contact)];
        return ResponseHelper::jsonRespones($data,'Contact deleted successfully');
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
            return ResponseHelper::jsonRespones([], 'Contacts not found for this user', 404, false);
        }

        $data = ['contacts' => ContactResource::collection($contacts)];
        return ResponseHelper::jsonRespones($data,'All Contacts deleted successfully');
    }
}
