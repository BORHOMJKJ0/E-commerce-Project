<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User Model",
 *     description="User details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the user"),
 *     @OA\Property(property="First_Name", type="string", example="John",description="The first name of the user"),
 *     @OA\Property(property="Last_Name", type="string", example="Doe", nullable=true,description="The second name of the user"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com",description="The email of the user"),
 *     @OA\Property(property="fcm_token", type="string", example="sample_fcm_token", nullable=true,description="The fcm token of the user"),
 *     @OA\Property(property="mobile", type="string", example="1234567890",description="The phone number of the user"),
 *     @OA\Property(property="Address", type="string", example="123 Main St", nullable=true,description="The address of the user"),
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function contacts()
    {
        return $this->hasMany(Contact_information::class);
    }

    public function expressions()
    {
        return $this->hasMany(Expression::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorite_products')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewedProducts()
    {
        return $this->belongsToMany(Product::class, 'reviews')
            ->withPivot('rating');
    }
}
