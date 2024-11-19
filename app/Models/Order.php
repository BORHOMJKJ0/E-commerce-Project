<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Order extends Model
{
    use HasFactory, Prunable;

    protected $guarded = [];
    protected $table = 'orders';

    public function orderItems()
    {
        return $this->hasMany(Order_item::class);
    }
    public function prunable()
    {
        return static::where('status', 'delivered');
    }


}
