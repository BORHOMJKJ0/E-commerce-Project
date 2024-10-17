<?php

namespace App\Repositories;

use App\Models\Offer;
use App\Traits\Lockable;

class OfferRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Offer::paginate($items, ['*'], 'page', $page);
    }

    public function getMy($items, $page)
    {
        return Offer::whereHas('warehouse.product', function ($query) {
            $query->where('user_id', auth()->id());
        })->paginate($items, ['*'], 'page', $page);
    }

    public function orderMyBy($column, $direction, $page, $items)
    {
        return Offer::whereHas('warehouse.product', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Offer::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return $this->lockForCreate(function () use ($data) {
            return Offer::create($data);
        });
    }

    public function update(Offer $offer, array $data)
    {
        return $this->lockForUpdate(Offer::class, $offer->id, function ($lockedOffer) use ($data) {
            $lockedOffer->update($data);

            return $lockedOffer;
        });
    }

    public function delete(Offer $offer)
    {
        return $this->lockForDelete(Offer::class, $offer->id, function ($lockedOffer) {
            return $lockedOffer->delete();
        });
    }
}
