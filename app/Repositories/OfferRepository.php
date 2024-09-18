<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public function getAll()
    {
        return Offer::paginate(5);
    }

    public function orderBy($column, $direction)
    {
        return Offer::orderBy($column, $direction)->paginate(5);
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
