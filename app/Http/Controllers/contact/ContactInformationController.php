<?php

namespace App\Http\Controllers\contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Services\Contact_InformationService;
use Illuminate\Http\Request;

class ContactInformationController extends Controller
{
    protected $contact_informationService;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->contact_informationService = new contact_informationService;
    }

    public function addContact(ContactRequest $request)
    {
        return $this->contact_informationService->addContact($request);
    }

    public function show()
    {
        return $this->contact_informationService->show();
    }

    public function delete_certain_contact(Request $request)
    {
        return $this->contact_informationService->delete_certain_contact($request);
    }

    public function delete_all_contact()
    {
        return $this->contact_informationService->delete_all_contact();
    }
}
