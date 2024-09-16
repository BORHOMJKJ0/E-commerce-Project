<?php

namespace App\Http\Controllers\contact;

use App\Http\Controllers\Controller;
use App\Models\contact_type;
use App\Traits\ValidationTrait;

class ContactTypeController extends Controller
{
    use ValidationTrait;

    public function index()
    {
        return contact_type::all()->select('id', 'type_arabic', 'type_english');
    }

    public function show($id)
    {
        $contact_type = contact_type::find($id);
        $data = [
            'id' => $contact_type->id,
            'type_arabic' => $contact_type->type_arabic,
            'type_english' => $contact_type->type_english,
        ];

        return response()->json($data, 200);
    }
}
