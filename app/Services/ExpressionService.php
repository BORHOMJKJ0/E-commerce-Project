<?php

namespace App\Services;

use App\Http\Requests\ExpressionRequest;
use App\Models\Expression;
use App\Models\Product;
use App\Models\User;
use App\Repositories\ExpressionRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\Request;

class ExpressionService
{
    use ValidationTrait;

    private $expressionRepository;

    public function __construct(ExpressionRepository $expressionRepository)
    {
        //        $this->expressionRepository = $expressionRepository;
    }

    public function index()
    {
        $products = Product::all();
        $all_Product = [];

        foreach ($products as $product) {
            $message = $this->expressionRepository->Expressions_Product($product->id);
            $all_Product[] = $message;
        }

        return response($all_Product, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/expression/add",
     *     summary="Create an Expression",
     *     tags={"Expressions"},
     *     description="Create a new expression with product_id,id for auth user, and an optional action",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"product_id", "user_id"},
     *
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="action", type="enum", example="like")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Expression created successfully",
     *
     *         @OA\JsonContent(
     *
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
     *              )
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
     *             @OA\Property(property="message", type="string", example="action input is invalid")
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Prodcut Not Found")
     *          )
     *      )
     * )
     */
    public function create(ExpressionRequest $request)
    {

        $responseValidation = $this->validateRequest($request, $request->rules());
        if ($responseValidation) {
            return $responseValidation;
        }

        $expression = $this->expressionRepository->create($request);
        $message = [
            'expression' => [
                'expression_id' => $expression->id,
                'product_id' => (int) $expression->product_id,
                'user_id' => $expression->user_id,
                'action' => $expression->action ?? 'No Action',
            ],
        ];

        return response()->json($message, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/expression/show/{product_id}",
     *     summary="Get product expressions including views, likes, and dislikes",
     *     description="Returns the number of views, likes, dislikes, and the list of users who interacted with the product.",
     *     operationId="Expressions_Product",
     *     tags={"Expressions"},
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
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
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     * )
     */
    public function show(Product $product)
    {
        $message = $this->expressionRepository->Expressions_Product($product->id);

        return response()->json($message, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/users/expression/update/{product_id}",
     *     summary="Update user expression for a product",
     *     description="Allows a user to update their expression (like or dislike) for a specific product.",
     *     operationId="updateExpression",
     *     tags={"Expressions"},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="action",
     *                 type="string",
     *                 enum={"like", "dislike"},
     *                 description="The expression action, either 'like' or 'dislike'.",
     *                 example="like"
     *             )
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
     *             )
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
     *             @OA\Property(property="message", type="string", example="User not expressed for this product")
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
     *             @OA\Property(property="message", type="string", example="action input is invalid")
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $product)
    {
        $responseValidation = $this->validateRequest($request, [
            'action' => 'sometimes|in:like,dislike',
        ]);

        if ($responseValidation) {
            return $responseValidation;
        }

        $user = User::find(auth()->id());
        $expression = Expression::where('user_id', auth()->id())->where('product_id', $product->id)->first();
        if (! $expression) {
            return response()->json(['message' => 'User not expressed for this product'], 404);
        }

        if ($request->filled('action')) {
            $data['action'] = $request->get('action');
        } else {
            $data['action'] = null;
        }

        $user->expressions()->update($data);
        $message = [
            'message' => 'updated successfully',
            'user' => [
                'id' => auth()->id(),
                'name' => $user->name,
            ],
            'expression' => [
                'action' => $request->action ?? 'No Action',
            ],
        ];

        return response()->json($message, 200);
    }
}
