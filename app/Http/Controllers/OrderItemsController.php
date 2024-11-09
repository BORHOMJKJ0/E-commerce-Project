<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\Cart_items;
use App\Models\Order;
use App\Models\Order_items;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemsController extends Controller
{
    protected $orderController;

    // protected $fcmService;

    public function __construct(OrderController $orderController,
        //FcmService      $fcmService
    ) {
        $this->middleware('auth');
        $this->orderController = $orderController;
        //$this->fcmService = $fcmService;
    }

    public function placeOrder(Request $request): JsonResponse
    {
        $user_id = auth()->user()->id;
        $cart = Cart::where('user_id', $user_id)->first();

        $cartItems = $cart->cart_items()->with('warehouse.product.user')->get();

        if ($cartItems->isEmpty()) {
            return ResponseHelper::jsonResponse([], 'there is no products in cart');
        }

        $unorderedItems = [];

        $order = new Order;
        foreach ($cartItems as $item) {
            $warehouse = $item->warehouse;
            $productOwner_id = $item->warehouse->product->user->id;
            if ($item->quantity <= $warehouse->amount && Carbon::now()->lessThanOrEqualTo($warehouse->expiry_date)) {
                $order = $this->orderController->findOrCreate($user_id, $productOwner_id);
                $price = $this->calculatePrice($item);
                $data = [
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'order_id' => $order->id,
                    'warehouse_id' => $warehouse->id,
                ];
                Order_items::create($data);
            } else {
                $unorderedItems[] = [
                    'product_name' => $item->warehouse->product->name,
                    'reason' => $item->quantity > $warehouse->amount ? 'Insufficient stock' : 'Expired',
                ];
            }

        }
        $this->updateOrderTotalPrice($order);
        $cart->cart_items()->delete();


        //firebase notification to device user
        //$this->sendMessageForUnorderedItems($unorderedItems, $cart);

        return ResponseHelper::jsonResponse([], 'cart has ordered items');
    }

    public function updateOrderTotalPrice(Order $order)
    {
        $totalPrice = $order->orderItems->sum(fn ($item) => $item->price * $item->quantity);
        $order->update(['total_price' => $totalPrice]);
    }

    public function calculatePrice(Cart_items $item)
    {
        $greatestOffer = $item->warehouse->offers()
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->orderByDesc('discount_percentage')
            ->first();

        $price = $item->warehouse->product->price;

        if ($greatestOffer) {
            $price = $price - ($price * ($greatestOffer->discount_percentage / 100));
        }

        return $price;
    }

    public function sendMessageForUnorderedItems(array $unorderedItems, Cart $cart): void
    {
        if (! empty($unorderedItems)) {
            $user = $cart->user;
            $message = "Some items in your cart can't be ordered :\n";
            foreach ($unorderedItems as $item) {
                $message .= $item['product_name'].' '.$item['reason']."\n";
            }
            $this->fcmService->sendNotification($user->fcm_token, 'Unordered Cart Items', $message);
        }
    }
}
