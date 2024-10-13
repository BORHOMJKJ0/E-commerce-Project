<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Contact Type",
 *     title="Contact Type Model",
 *     description="Contact Type details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of Contact type"),
 *     @OA\Property(property="type_english", type="string", example="Email", description="The English name of the contact type"),
 *     @OA\Property(property="type_arabic", type="string", example="بريد إلكتروني", description="The Arabic name of the contact type"),
 * )
 */
class Contact_type extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contact_information()
    {
        return $this->belongsTo(Contact_information::class);
    }
}
