<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Traits\AuthTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentService
{
    use AuthTrait;

    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Enter JWT Bearer token in the format 'Bearer {token}'"
     * )
     */
    /**
     * @OA\Get(
     *     path="/api/comments",
     *     summary="Get all comments",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CommentResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function getAllComments(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $comments = $this->commentRepository->getAll($items, $page);
        $hasMorePages = $comments->hasMorePages();

        $data = [
            'Comments' => CommentResource::collection($comments),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Comments retrieved successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/comments/my",
     *     summary="Get My comments",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CommentResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function getMyComments(Request $request)
    {
        $page = $request->query('page', 1);
        $items = $request->query('items', 20);

        $comments = $this->commentRepository->getMy($items, $page);
        $hasMorePages = $comments->hasMorePages();

        $data = [
            'Comments' => CommentResource::collection($comments),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Comments retrieved successfully!');
    }

    /**
     * @OA\Get(
     *     path="/api/comments/{id}",
     *     summary="Get a comment by ID",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Comment ID you want to show it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CommentResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Comment not found")
     *         )
     *     )
     * )
     */
    public function getCommentById(Comment $comment)
    {
        $data = ['comment' => CommentResource::make($comment)];

        return ResponseHelper::jsonResponse($data, 'Comments retrieved successfully!');
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Create a comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *                   required={"review_id"},
     *
     *                 @OA\Property(property="review_id", type="integer", example=1, description="The ID of your review"),
     *                 @OA\Property(property="title", type="string", example="This is a comment title", description="The comment title. Must provide either 'text' or 'image', or both."),
     *                 @OA\Property(property="text", type="string", example="This is a comment", description="The comment text. Must provide either 'text' or 'image', or both."),
     *                 @OA\Property(property="image", type="string", format="binary", description="Optional image. Must provide either 'text' or 'image', or both.")
     *             )
     *         )
     *     ),
     *
     *    @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Comment created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="My opinion"),
     *             @OA\Property(property="text", type="string", example="Thank you very much!"),
     *             @OA\Property(property="image", type="string", example="http://example.com/image.jpg", nullable=true),
     *             @OA\Property(property="product_name", type="string", example="apple"),
     *             @OA\Property(property="user_name", type="string", example="Only"),
     *             )
     *           ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="The text field is required when none of image are present.")
     *         )
     *     ),
     * )
     */
    public function createComment(array $data)
    {
        $this->validateCommentData($data, 'required', 'create');
        $this->checkReviewOwnership($data['review_id']);
        $this->checkReview($data['review_id']);
        $comment = $this->commentRepository->create($data);
        $data = ['Comment' => CommentResource::make($comment)];

        return ResponseHelper::jsonResponse($data, 'Comment created successfully!', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/comments/order/{column}/{direction}",
     *     summary="Order comments by a specific column",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *     description="Column you want to order the comments by it",
     *
     *         @OA\Schema(type="string", enum={"created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *     description="Dircetion of ordering",
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page ",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CommentResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction or parameters")
     *         )
     *     )
     * )
     */
    public function getCommentsOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $comments = $this->commentRepository->orderBy($column, $direction, $page, $items);
        $hasMorePages = $comments->hasMorePages();

        $data = [
            'Comments' => CommentResource::collection($comments),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Comments ordered successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/comments/my/order/{column}/{direction}",
     *     summary="Order My comments by a specific column",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="column",
     *         in="path",
     *         required=true,
     *         description="Column you want to order the comments by it",
     *
     *         @OA\Schema(type="string", enum={"created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="direction",
     *         in="path",
     *         required=true,
     *     description="Dircetion of ordering",
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="items",
     *         in="query",
     *         required=false,
     *         description="Number of items per page ",
     *
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CommentResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid column or direction",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid column or direction or parameters")
     *         )
     *     )
     * )
     */
    public function getMyCommentsOrderedBy($column, $direction, Request $request)
    {
        $validColumns = ['created_at', 'updated_at'];
        $validDirections = ['asc', 'desc'];

        if (! in_array($column, $validColumns) || ! in_array($direction, $validDirections)) {
            return ResponseHelper::jsonResponse([], 'Invalid column or direction', 400, false);
        }

        $page = $request->query('page', 1);
        $items = $request->query('items', 20);
        $comments = $this->commentRepository->orderMyBy($column, $direction, $page, $items);
        $hasMorePages = $comments->hasMorePages();

        $data = [
            'Comments' => CommentResource::collection($comments),
            'hasMorePages' => $hasMorePages,
        ];

        return ResponseHelper::jsonResponse($data, 'Comments ordered successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     summary="Update a comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Comment ID you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *     description="Comment Text",
     *
     *         @OA\Schema(type="string", example="My opinion")
     *     ),
     *
     *     @OA\Parameter(
     *         name="text",
     *         in="query",
     *         required=false,
     *     description="Comment Text",
     *
     *         @OA\Schema(type="string", example="Thank you!")
     *     ),
     *
     *   @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=false,
     *     description="Comment Image",
     *
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *
     *     @OA\Parameter(
     *         name="review_id",
     *         in="query",
     *         required=false,
     *     description="Review ID you want to update it",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *
     *         @OA\Schema(type="string", example="application/json")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="My opinion"),
     *             @OA\Property(property="text", type="string", example="Thank you very much!"),
     *             @OA\Property(property="image", type="string", example="https://example.com/images/smartphone-xyz.jpg"),
     *             @OA\Property(property="product_name", type="string", example="apple"),
     *             @OA\Property(property="user_name", type="string", example="Only"),
     *             @OA\Property(property="review_id", type="integer", example=1),
     *      ),
     *    ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to update this Comment.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Comment not found")
     *         )
     *     )
     * )
     */
    public function updateComment(Comment $comment, array $data)
    {
        try {
            $this->validateCommentData($data, 'sometimes');
            $this->checkComment($comment, 'Comment', 'update');

            $comment = $this->commentRepository->update($comment, $data);

            $data = ['comment' => CommentResource::make($comment)];
            $response = ResponseHelper::jsonResponse($data, 'Comment updated successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     summary="Delete a comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="Comment ID you want to delete it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Comment deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this Comment.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Comment not found")
     *         )
     *     )
     * )
     */
    public function deleteComment(Comment $comment)
    {
        try {
            $this->checkComment($comment, 'Comment', 'delete');
            $this->commentRepository->delete($comment);
            $response = ResponseHelper::jsonResponse([], 'Comment deleted successfully!');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }

    protected function validateCommentData(array $data, $rule = 'required', $method = 'any')
    {
        $validator = Validator::make($data, [
            'title' => 'required_without_all:text|nullable|string|max:255',
            'text' => 'required_without_all:image|nullable|string|max:1000',
            'image' => 'required_without_all:title|nullable|image|max:5120',
            'review_id' => "$rule|exists:reviews,id",
        ],
            [
                'text.required_without_all' => 'You must provide either an image or both title and text.',
                'title.required_without_all' => 'You must provide either an image or both title and text.',
                'image.required_without_all' => 'You must provide either an image or both title and text.',
            ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                ResponseHelper::jsonResponse([], $validator->errors()->first(), 400, false)
            );
        }
    }
}
