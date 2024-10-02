<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Exceptions\HttpResponseException;
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
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $comments = $this->commentService->getAllComments($page, $items);
        $hasMorePages = $comments->hasMorePages();

        return response()->json([
            'comments' => CommentResource::collection($comments),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);
    }

    public function MyComments(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $coments = $this->commentService->getMyComments($page, $items);
        $hasMorePages = $coments->hasMorePages();

        return response()->json([
            'comments' => CommentResource::collection($coments),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $comment = $this->commentService->createComment($request->all());

        return response()->json([
            'message' => 'Comment created successfully!',
            'comment' => CommentResource::make($comment),
            'successful' => true,
        ], 201);
    }

    public function show(Comment $comment): JsonResponse
    {
        $comment = $this->commentService->getCommentById($comment);

        return response()->json([
            'message' => 'Comment performed successfully!',
            'comment' => CommentResource::make($comment),
            'successful' => true,
        ], 200);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $comments = $this->commentService->getCommentsOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $comments->hasMorePages();

        return response()->json([
            'comments' => CommentResource::collection($comments),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);

    }

    public function MyCommentsOrderBy($column, $direction, Request $request): JsonResponse
    {
        $validColumns = ['created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return response()->json(['error' => 'Invalid column or direction', 'successful' => false], 400);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $comments = $this->commentService->getMyCommentsOrderedBy($column, $direction, $page, $items);
        $hasMorePages = $comments->hasMorePages();

        return response()->json([
            'comments' => CommentResource::collection($comments),
            'hasMorePages' => $hasMorePages,
            'successful' => true,
        ], 200);

    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        try {

            $comment = $this->commentService->updateComment($comment, $request->all());

            return response()->json([
                'message' => 'Comment updated successfully!',
                'comment' => CommentResource::make($comment),
                'successful' => true,
            ], 200);
        } catch (HttpResponseException $e) {
            return response()->json($e->getResponse()->getData(), 403);
        }
    }

    public function destroy(Comment $comment): JsonResponse
    {
        try {

            $this->commentService->deleteComment($comment);

            return response()->json(['message' => 'Comment deleted successfully!', 'successful' => true], 200);
        } catch (HttpResponseException $e) {
            return response()->json($e->getResponse()->getData(), 403);
        }

    }
}
