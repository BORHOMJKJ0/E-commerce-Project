<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment ? [
                'id' => $this->comment->id,
                'text' => $this->comment->text ?? null,
                'image' => $this->comment->image ?? null,
            ] : null,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
