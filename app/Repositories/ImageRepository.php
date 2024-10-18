<?php

namespace App\Repositories;

use App\Models\Image;
use App\Traits\Lockable;

class ImageRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Image::paginate($items, ['*'], 'page', $page);
    }

    public function getMy($items, $page)
    {
        return Image::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->paginate($items, ['*'], 'page', $page);
    }

    public function orderMyBy($column, $direction, $page, $items)
    {
        return Image::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Image::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return $this->lockForCreate(function () use ($data) {
            return Image::create($data);
        });
    }

    public function update(Image $Image, array $data)
    {
        return $this->lockForUpdate(Image::class, $Image->id, function ($lockedImage) use ($data) {
            $lockedImage->update($data);

            return $lockedImage;
        });
    }

    public function delete(Image $Image)
    {
        return $this->lockForDelete(Image::class, $Image->id, function ($lockedImage) {
            return $lockedImage->delete();
        });
    }
}
