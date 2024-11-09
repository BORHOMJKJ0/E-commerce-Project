<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order_items extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $guarded = [];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
