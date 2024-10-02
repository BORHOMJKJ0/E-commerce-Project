<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Traits\Lockable;

class CommentRepository
{
    use Lockable;

    public function getAll($items, $page)
    {
        return Comment::paginate($items, ['*'], 'page', $page);
    }

    public function getMy($items, $page)
    {
        return Comment::with('review')->whereHas('review', function ($query) {
            $query->where('user_id', auth()->id());
        })->paginate($items, ['*'], 'page', $page);
    }

    public function orderMyBy($column, $direction, $page, $items)
    {
        return Comment::whereHas('review', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->orderBy($column, $direction)
            ->paginate($items, ['*'], 'page', $page);
    }

    public function orderBy($column, $direction, $page, $items)
    {
        return Comment::orderBy($column, $direction)->paginate($items, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        return $this->lockForCreate(function () use ($data) {
            return Comment::create($data);
        });
    }

    public function update(Comment $comment, array $data)
    {
        return $this->lockForUpdate(Comment::class, $comment->id, function ($lockedComment) use ($data) {
            $lockedComment->update($data);

            return $lockedComment;
        });
    }

    public function delete(Comment $comment)
    {
        return $this->lockForDelete(Comment::class, $comment->id, function ($lockedComment) {
            return $lockedComment->delete();
        });
    }
}
