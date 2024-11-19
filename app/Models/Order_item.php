<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order_item extends Model
{
    use HasFactory,Prunable;

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
    public function prunable()
    {
        return static::whereHas('warehouse', function ($query) {
            $query->where('expiry_date', '<', now());
        });
    }
}
