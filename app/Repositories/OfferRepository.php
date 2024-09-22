<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public function getAll($items, $page)
    {
        return Offer::paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Offer::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return Offer::create($data);
    }

    public function update(Offer $offer, array $data)
    {
        $offer->update($data);

        return $offer;
    }

    public function delete(Offer $offer)
    {
        return $offer->delete();
    }
}
