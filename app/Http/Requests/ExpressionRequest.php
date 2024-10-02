<?php

namespace App\Http\Requests;

use App\Repositories\ExpressionRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpressionRequest extends BaseRequest
{
    private $expressionRepository;

    public function __construct(ExpressionRepository $expressionRepository)
    {
        parent::__construct();
        $this->expressionRepository = $expressionRepository;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isAddExpression(auth()->user()->id, $this->request->get('product_id'))) {
            return false;
        }

        return true;
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'You are not authorized to add expression more than one',
            'success' => false,
        ], 403));
    }

    public function isAddExpression($user_id, $product_id): bool
    {
        $expression = $this->expressionRepository->findByUser_Product($user_id, $product_id);
        if ($expression) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'action' => 'sometimes|in:like,dislike',
        ];
    }
}
