<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);

    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    //    public function offers()
    //    {
    //        return $this->hasmany(Offer::class);
    //    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
