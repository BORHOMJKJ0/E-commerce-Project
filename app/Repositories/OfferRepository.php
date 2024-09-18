<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public function getAll()
    {
        return Offer::with('product')->paginate(5);
    }

    public function findById($id)
    {
        return Offer::with('product')->findOrFail($id);
    }

    public function orderBy($column, $direction)
    {
        return Offer::with('product')->orderBy($column, $direction)->paginate(5);
    }

    public function create(array $data)
    {
        return Offer::create($data);
    }

    public function update($id, array $data)
    {
        $offer = Offer::findOrFail($id);
        $offer->update($data);

        return $offer;
    }

    public function delete($id)
    {
        $offer = Offer::findOrFail($id);

        return $offer->delete();
    }
}
