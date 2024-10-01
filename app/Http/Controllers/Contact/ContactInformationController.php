<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\User;
use App\Services\Contact_InformationService;

class ContactInformationController extends Controller
{
    protected $contact_informationService;

    public function __construct(Contact_InformationService $contact_InformationService)
    {
        $this->middleware('auth:api');
        $this->contact_informationService = $contact_InformationService;
    }

    public function store(ContactRequest $request)
    {
        return $this->contact_informationService->addContact($request);
    }

    public function show(User $user)
    {
        return $this->contact_informationService->show($user->id);
    }

    public function update(UpdateContactRequest $request, $user_id, $contact_id)
    {
        return $this->contact_informationService->update($request, $user_id, $contact_id);
    }

    public function destroy($contact_information_id)
    {
        return $this->contact_informationService->destroy($contact_information_id);
    }

    public function destroyAll()
    {
        return $this->contact_informationService->destroyAll();
    }
}
