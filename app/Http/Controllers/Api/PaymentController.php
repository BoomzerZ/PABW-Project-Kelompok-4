<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Simulate a payment process.
     */
    public function pay(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);

        if ($order->status === 'completed') {
            return response()->json(['message' => 'Order is already paid and completed'], 400);
        }

        // Simulate successful payment
        $order->status = 'completed';
        $order->save();

        return response()->json([
            'message' => 'Payment successful (simulated)',
            'order' => $order->load('items.product')
        ]);
    }
}
