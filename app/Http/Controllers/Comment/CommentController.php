<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->middleware('auth:api');
        $this->commentService = $commentService;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->commentService->getAllComments($request);
    }

    public function MyComments(Request $request): JsonResponse
    {
        return $this->commentService->getMyComments($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->commentService->createComment($request->all());
    }

    public function show(Comment $comment): JsonResponse
    {
        return $this->commentService->getCommentById($comment);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->commentService->getCommentsOrderedBy($column, $direction, $request);
    }

    public function MyCommentsOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->commentService->getMyCommentsOrderedBy($column, $direction, $request);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        return $this->commentService->updateComment($comment, $request->all());
    }

    public function destroy(Comment $comment): JsonResponse
    {
        return $this->commentService->deleteComment($comment);
    }
}
