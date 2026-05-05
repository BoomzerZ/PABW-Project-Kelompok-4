<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create a new order (checkout).
     */
    public function store(Request $request)
    {
        $user = $request->user();
        return DB::transaction(function () use ($user, $request) {
            // Lock the cart items and their products
            $cartItems = Cart::with(['product' => function ($query) {
                $query->lockForUpdate();
            }])->where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Cart is empty'], 400);
            }

            // Validation: Check stock
            foreach ($cartItems as $cartItem) {
                if ($cartItem->quantity > $cartItem->product->stock) {
                    return response()->json([
                        'message' => "Insufficient stock for {$cartItem->product->name}. Available: {$cartItem->product->stock}"
                    ], 400);
                }
            }

            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $discount = 0;
            $coupon = null;

            if ($request->filled('coupon_code')) {
                $coupon = Coupon::where('code', $request->coupon_code)->lockForUpdate()->first();
                if ($coupon && $coupon->isValid()) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }

            $totalPrice = $subtotal - $discount;

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);
                
                // Stock decrement moved to Order model event (status changed to completed)
            }

            // Update coupon usage
            if ($coupon) {
                $coupon->increment('used_count');
            }

            // Clear cart
            Cart::where('user_id', $user->id)->delete();

            return response()->json($order->load('items.product'), 201);
        });
    }

    /**
     * List user orders.
     */
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Get specific order details.
     */
    public function show(Request $request, $id)
    {
        $order = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($order);
    }
}
