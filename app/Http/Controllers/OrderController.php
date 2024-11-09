<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
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
}
