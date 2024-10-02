<?php

namespace App\Repositories;

use App\Models\Review;
use App\Traits\Lockable;

class ReviewRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Review::paginate($items, ['*'], 'page', $page);
    }

    public function getMy($items, $page)
    {
        return Review::where('user_id', auth()->id())
            ->paginate($items, ['*'], 'page', $page);
    }

    public function orderMyBy($column, $direction, $page, $items)
    {
        return Review::where('user_id', auth()->id())
            ->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Review::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return $this->lockForCreate(function () use ($data) {
            return Review::create($data);
        });
    }

    public function update(Review $review, array $data)
    {
        return $this->lockForUpdate(Review::class, $review->id, function ($lockedReview) use ($data) {
            $lockedReview->update($data);

            return $lockedReview;
        });
    }

    public function delete(Review $review)
    {
        return $this->lockForDelete(Review::class, $review->id, function ($lockedReview) {
            return $lockedReview->delete();
        });
    }
}
