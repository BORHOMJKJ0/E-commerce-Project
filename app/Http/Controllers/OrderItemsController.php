<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\Cart_items;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemsController extends Controller
{
    protected $orderController;

    protected $warehouseRepository;

    protected $fcmService;

    public function __construct(OrderController $orderController, WarehouseRepository $warehouseRepository,
        //FcmService      $fcmService
    ) {
        $this->middleware('auth');
        $this->orderController = $orderController;
        $this->warehouseRepository = $warehouseRepository;
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

            if ($this->isValidOrderItem($item, $warehouse)) {
                $order = $this->createOrderItem($user_id, $productOwner_id, $item, $warehouse);
            } else {
                $unorderedItems[] = [
                    'product_name' => $item->warehouse->product->name,
                    'reason' => $item->quantity > $warehouse->amount ? 'Insufficient stock' : 'Expired',
                ];
            }

        }
        $orders = Order::where('customer_id', $user_id)->with('orderItems')->get();
        $this->updateOrderTotalPrice($orders);

        $cart->cart_items()->delete();

        //firebase notification to device user
        //$this->sendMessageForUnorderedItems($unorderedItems, $cart);

        return ResponseHelper::jsonResponse([], 'cart has ordered items');
    }

    public function isValidOrderItem(Cart_items $item, Warehouse $warehouse): bool
    {
        return $item->quantity <= $warehouse->amount && Carbon::now()->lessThanOrEqualTo($warehouse->expiry_date);
    }

    public function createOrderItem($user_id, $productOwner_id, $item, $warehouse)
    {
        $this->warehouseRepository->update($warehouse, ['amount' => $warehouse->amount - $item->quantity]);
        $order = $this->orderController->findOrCreate($user_id, $productOwner_id);
        $price = $this->calculatePrice($item);
        $data = [
            'quantity' => $item->quantity,
            'price' => $price,
            'order_id' => $order->id,
            'warehouse_id' => $warehouse->id,
        ];

        Order_item::create($data);

        return $order;
    }

    public function updateOrderTotalPrice($orders)
    {
        foreach ($orders as $order) {
            $totalPrice = $order->orderItems->sum(fn ($item) => $item->price * $item->quantity);
            $order->update(['total_price' => $totalPrice]);
        }
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
