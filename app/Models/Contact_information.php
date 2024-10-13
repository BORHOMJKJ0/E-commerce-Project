<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Contact Information",
 *     title="Contact Info Model",
 *     description="ContactInfo details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the contact information"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="The ID of the associated user"),
 *     @OA\Property(property="contact_type_id", type="integer", example=1, description="The ID of the associated contact type"),
 *     @OA\Property(property="link", type="string", example="http://example.com/contact", description="The contact link"),
 * )
 */
class Contact_information extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact_type()
    {
        return $this->hasOne(Contact_type::class);
    }
}
