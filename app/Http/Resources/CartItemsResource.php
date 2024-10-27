<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'cart' => $this->cart ? [
                'id' => $this->cart->id,
                'user_name' => optional($this->cart->user)->first_name.' '.optional($this->cart->user)->last_name,
            ] : null,
            'product' => $this->warehouse && $this->warehouse->product ? [
                'id' => $this->warehouse->product->id,
                'name' => $this->warehouse->product->name,
                'category' => optional($this->warehouse->product->category)->name,
                'user' => [
                    'id' => optional($this->warehouse->product->user)->id,
                    'name' => optional($this->warehouse->product->user)->first_name.' '.optional($this->warehouse->product->user)->last_name,
                ],
            ] : null,
        ];
    }
}
