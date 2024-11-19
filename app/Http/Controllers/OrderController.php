<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function myOrders(Request $request)
    {
        $user_id = auth()->user()->id;
        $myOrders = Order::where('customer_id', $user_id)->with('orderItems.warehouse.product')->get();

        //return response()->json($myOrders);
        return response()->json(OrderResource::collection($myOrders), 200);
    }

    public function findOrCreate($customer_id, $seller_id)
    {
        $data = [
            'customer_id' => $customer_id,
            'seller_id' => $seller_id,
        ];

        $order = Order::where($data)->first();

        if (! $order) {
            $order = Order::create($data);
        }

        return $order;
    }

    public function delete(Request $request, Order $order)
    {
        if ($order->status == 'pending') {
            $order->delete();

            return ResponseHelper::jsonResponse([], 'Order has been canceled successfully', 200);
        } elseif ($order->status == 'accepted') {
            return ResponseHelper::jsonResponse([], "You can't cancel the order because it is being processed", 403);
        }
        return ResponseHelper::jsonResponse([], '');
    }
}
