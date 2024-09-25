<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Services\Contact_InformationService;
use App\Models\User;

class ContactInformationController extends Controller
{
    protected $contact_informationService;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->contact_informationService = new contact_informationService;
    }

    public function store(ContactRequest $request)
    {
        return $this->contact_informationService->addContact($request);
    }

    public function show(User $user)
    {
        return $this->contact_informationService->show($user->id);
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
