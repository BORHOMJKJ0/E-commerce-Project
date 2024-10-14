<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ExpressionRequest;
use App\Http\Requests\UpdateExpressionRequest;
use App\Models\Expression;
use App\Models\Product;
use App\Models\User;
use App\Repositories\ExpressionRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpressionService
{
    use ValidationTrait;

    private $expressionRepository;

    public function __construct(ExpressionRepository $expressionRepository)
    {
        $this->expressionRepository = $expressionRepository;
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
     *     path="/api/users/expression/all",
     *     summary="Get all expressions",
     *     tags={"Expressions"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CategoryResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $products = Product::all();
        $all_Product = [];

        foreach ($products as $product) {
            $message = $this->expressionRepository->Expressions_Product($product->id);
            $all_Product[] = $message;
        }

        $data = ['products' => $all_Product];

        return ResponseHelper::jsonResponse($data, 'Expressions for all products is retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/users/expression/add",
     *     summary="Create an Expression",
     *     tags={"Expressions"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *             required={"product_id", "user_id"},
     *
     *             @OA\Property(property="product_id", type="integer", example=1,description="Product ID you want to add expression to it",),
     *             @OA\Property(property="action", type="enum", example="like",description="the Action Like or dislike")
     *         )
     *       )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Expression created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful",type="boolean",example=true),
     *             @OA\Property(property="expression", type="object",
     *                 @OA\Property(property="expression_id", type="integer", example=1),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                @OA\Property(
     *                  property="action",
     *                  type="string",
     *                  enum={"like", "dislike"},
     *                  description="The expression action, either 'like' or 'dislike'.",
     *                  example="like"
     *              ),
     *              @OA\Property(property="status_code", type="integer", example=201),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful",type="boolean",example=false),
     *             @OA\Property(property="message", type="string", example="action input is invalid"),
     *              @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="successful",type="boolean",example=false),
     *              @OA\Property(property="message", type="string", example="Prodcut Not Found"),
     *               @OA\Property(property="status_code", type="integer", example=404),
     *          )
     *      )
     * )
     */
    public function create(ExpressionRequest $request): JsonResponse
    {
        // return response()->json($request->all());
        $expression = $this->expressionRepository->create($request->only('action', 'product_id'));
        $data = [
            'expression' => [
                'expression_id' => $expression->id,
                'product_id' => (int) $expression->product_id,
                'user_id' => $expression->user_id,
                'action' => $expression->action ?? 'No Action',
            ],
        ];

        return ResponseHelper::jsonResponse($data, 'Expression created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/expression/show/{product_id}",
     *     summary="Get product expressions including views, likes, and dislikes",
     *     tags={"Expressions"},
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *
     *         @OA\Schema(type="integer",example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="successful",type="boolean",example=true),
     *             @OA\Property(
     *                 property="product",
     *                 type="string",
     *                 description="Name of the product"
     *             ),
     *             @OA\Property(
     *                 property="expression",
     *                 type="object",
     *                 @OA\Property(
     *                     property="views",
     *                     type="object",
     *                     @OA\Property(property="number", type="integer", example=3, description="Number of views"),
     *                     @OA\Property(
     *                         property="users",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Hasan")
     *                         ),
     *                         example={
     *                             {"id": 1, "name": "Hasan"},
     *                             {"id": 2, "name": "Hadi"},
     *                            {"id": 3, "name": "Anas"},
     *                         }
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="likes",
     *                     type="object",
     *                     @OA\Property(property="number", type="integer", example=2, description="Number of likes"),
     *                     @OA\Property(
     *                         property="users",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Hasan")
     *                         ),
     *                         example={
     *                             {"id": 1, "name": "Hasan"},
     *                             {"id": 3, "name": "Anas"}
     *                         }
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="disLikes",
     *                     type="object",
     *                     @OA\Property(property="number", type="integer", example=1, description="Number of dislikes"),
     *                     @OA\Property(
     *                         property="users",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Hasan")
     *                         ),
     *                         example={
     *                             {"id": 1, "name": "Hasan"},
     *                         }
     *                     )
     *                 )
     *             ),
     *              @OA\Property(property="status_code", type="integer", example=200),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *
     *          @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="successful",type="boolean",example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="status_code",type="integer",example=400)
     *         )
     *     ),
     * )
     */
    public function show(Product $product)
    {
        $data = $this->expressionRepository->Expressions_Product($product->id);

        return ResponseHelper::jsonResponse($data, 'Expressions for product retrieved  successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/users/expression/update/{product_id}",
     *     summary="Update user expression for a product",
     *     tags={"Expressions"},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *
     *         @OA\Schema(type="integer",example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *              @OA\Property(
     *                 property="action",
     *                 type="string",
     *                 enum={"like", "dislike"},
     *                 description="The expression action, either 'like' or 'dislike'.",
     *                 example="like"
     *             )
     *           )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Expression updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="successful",type="boolean",example=true),
     *             @OA\Property(property="message", type="string", example="updated successfully"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Hasan")
     *             ),
     *             @OA\Property(
     *                 property="expression",
     *                 type="object",
     *                 @OA\Property(property="action", type="string", example="like")
     *             ),
     *             @OA\Property(property="status_code",type="integer",example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User has not expressed for this product",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="successful",type="boolean",example=false),
     *             @OA\Property(property="message", type="string", example="User not expressed for this product"),
     *             @OA\Property(property="status_code",type="integer",example=404)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="successful",type="boolean",example=false),
     *             @OA\Property(property="message", type="string", example="action input is invalid"),
     *             @OA\Property(property="status_code",type="integer",example=400)
     *         )
     *     ),
     * )
     */
    public function update(UpdateExpressionRequest $request, Product $product)
    {
        $expression = $this->expressionRepository->getExpressionForProduct($product->id);

        if (! $expression) {
            return ResponseHelper::jsonResponse([], 'User not expressed for this product', 404, false);
        }

        if ($request->filled('action')) {
            $data['action'] = $request->get('action');
        } else {
            $data['action'] = null;
        }

        $user = $this->expressionRepository->updateExpression($data);

        $message = [
            'message' => 'updated successfully',
            'user' => [
                'id' => auth()->id(),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ],
            'expression' => [
                'action' => $request->action ?? 'No Action',
            ],
        ];

        return ResponseHelper::jsonResponse($message, 'Expression For Product updated successfully');
    }
}
