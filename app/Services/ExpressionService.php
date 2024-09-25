<?php

namespace App\Services;

use App\Http\Requests\ExpressionRequest;
use App\Models\Expression;
use App\Models\Product;
use App\Repositories\ExpressionRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\Request;

class ExpressionService
{
    use ValidationTrait;

    private $expressionRepository;

    public function __construct(ExpressionRepository $expressionRepository)
    {
        $this->expressionRepository = $expressionRepository;
    }

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
            ],
        ];

        return response()->json($message, 201);
    }

    public function show(Product $product)
    {
        return $this->expressionRepository->Expressions_Product($product->id);
    }

    public function update(Request $request, $product)
    {
        $responseValidation = $this->validateRequest($request, [
            'action' => 'sometimes|in:like,dislike',
        ]);

        if ($responseValidation) {
            return $responseValidation;
        }

        $user = \App\Models\User::find(auth()->id());
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
                'action' => $request->action,
            ],
        ];

        return response()->json($message, 200);
    }
}
