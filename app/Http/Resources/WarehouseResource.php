<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pure_price' => $this->pure_price,
            'payment_date' => $this->payment_date,
            'settlement_date' => $this->settlement_date,
            'expiry_date' => $this->expiry_date,
            'product' => $this->Product ? [
                'name' => $this->Product->name,
                'price' => $this->Product->price,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User ? $this->Product->User->name : null,
                //                'offers' => $this->Product->Offers->map(function ($offer) {
                //                    return [
                //                        'discount' => $offer->discount_percentage,
                //                        'starting_at' => $offer->offer_start ? Carbon::parse($offer->offer_start)->format('Y-m-d H:i') : null,
                //                        'ending_at' => $offer->offer_end ? Carbon::parse($offer->offer_end)->format('Y-m-d H:i') : null,
                //                    ];
                //                }),
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
