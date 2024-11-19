<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'seller_id' => $this->seller_id,
            'order_items' => $this->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'warehouse' => [
                        'id' => $item->warehouse->id,
                        'amount' => $item->warehouse->amount,
                        'expiry_date' => $item->warehouse->expiry_date,
                    ],
                    'product' => [
                        'id' => $item->warehouse->product->id,
                        'name' => $item->warehouse->product->name,
                        'price' => $item->warehouse->product->price,
                    ],
                ];
            }),
        ];
    }
}
