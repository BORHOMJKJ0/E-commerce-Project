<?php

namespace App\Services;

use App\Http\Requests\ContactRequest;
use App\Repositories\ContactRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Contact_InformationService
{
    use ValidationTrait;

    protected $contactRepository;

    public function __construct()
    {
        $this->contactRepository = new ContactRepository;
    }

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

    public function show(Request $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, ['user_id' => 'required|exists:users,id|integer']);
        if ($validationResponse) {
            return $validationResponse;
        }

        $contacts = $this->contactRepository->findByUserId($request->user_id);
        if (! $contacts) {
            return response()->json(['message' => 'Contact information not found'], 404);
        }

        return response()->json(['contacts' => $contacts]);
    }

    public function delete_certain_contact($request): JsonResponse
    {
        $validateResponse = $this->validateRequest($request, [
            'contact_information_id' => 'required',
        ]);
        if ($validateResponse) {
            return $validateResponse;
        }

        $contact = $this->contactRepository->deleteById($request->contact_information_id);
        if (! $contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        return response()->json(['contact' => $contact, 'message' => 'Contact deleted successfully']);
    }

    public function delete_all_contact()
    {
        $contacts = $this->contactRepository->deleteByUser(auth()->user());
        if (! $contacts) {
            return response()->json(['message' => 'Contacts not found for this user'], 400);
        }

        return response()->json(['contacts' => $contacts, 'message' => 'All Contacts deleted successfully']);
    }
}
