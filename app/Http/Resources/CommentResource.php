<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'review_id' => $this->review->id,
            //            'product_id' => $this->review->product->id,
            //            'user_id' => $this->review->user->id,
            'product_name' => $this->review->product->name,
            'user_name' => $this->review->user->name,
            'text' => $this->text,
            'image' => $this->image,
        ];
    }
}
