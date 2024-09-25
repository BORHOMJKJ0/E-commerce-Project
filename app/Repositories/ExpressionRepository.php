<?php

namespace App\Repositories;

use App\Http\Requests\ExpressionRequest;
use App\Models\Expression;
use App\Models\Product;
use App\Traits\ValidationTrait;

class ExpressionRepository
{
    use ValidationTrait;

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(ExpressionRequest $request)
    {

        $user = $this->userRepository->findById(auth()->user()->id);

        $request->merge(['user_id' => $user->id]);

        return $user->expressions()->create($request->all());
    }

    public function Expressions_Product($product_id)
    {
        $views = Expression::all()->count();
        $likes = Expression::where('action', 'like')->count();
        $disLikes = Expression::where('action', 'dislike')->count();

        $data = [
            'views' => [
                'number' => $views,
                'users' => $this->usersWhoViewProduct($product_id),
            ],
            'likes' => [
                'number' => (int) $likes,
                'users' => $this->usersWhoAddExpression($product_id, 'like'),
            ],
            'disLikes' => [
                'number' => (int) $disLikes,
                'users' => $this->usersWhoAddExpression($product_id, 'dislike'),
            ],
        ];

        return response()->json(['expressions' => $data], 200);
    }

    public function usersWhoViewProduct($product_id)
    {
        $product = Product::find($product_id);

        return $product->expressions()->with('user:id,name')->get()->pluck('user');
    }

    public function usersWhoAddExpression($product_id, $expression)
    {
        $product = Product::find($product_id);

        return $product->expressions()->where('action', $expression)->with('user:id,name')->get()->pluck('user');
    }

    public function findByUser_Product($user_id, $product_id)
    {
        return Expression::where('user_id', $user_id)->where('product_id', $product_id)->first();
    }
}
