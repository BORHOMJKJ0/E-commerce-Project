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
            'cart' => $this->cart ? [
                'id' => $this->cart->id,
                'user_name' => optional($this->cart->user)->first_name.' '.optional($this->cart->user)->last_name,
            ] : null,
            'quantity' => $this->quantity,
            'product' => $this->product ? [
                'name' => $this->product->name,
                'user' => optional($this->product->user)->first_name.' '.optional($this->product->user)->last_name,
            ] : null,
        ];
    }
}
