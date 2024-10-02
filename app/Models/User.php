<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

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
