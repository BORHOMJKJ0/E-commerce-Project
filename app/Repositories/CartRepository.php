<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Traits\Lockable;

class CartRepository
{
    use Lockable;

    public function create()
    {
        $data = [
            'user_id' => auth()->id(),
        ];

        return $this->lockForCreate(function () use ($data) {
            return Cart::create($data);
        });
    }

    public function update(Cart $Cart)
    {

        return $this->lockForUpdate(Cart::class, $Cart->id, function ($lockedCart) {
            $data = [];
            $lockedCart->update($data);

            return $lockedCart;
        });
    }

    public function delete(Cart $Cart)
    {
        return $this->lockForDelete(Cart::class, $Cart->id, function ($lockedCart) {
            return $lockedCart->delete();
        });
    }
}
