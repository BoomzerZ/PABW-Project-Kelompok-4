<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Upload manual payment receipt.
     */
    public function pay(Request $request, $id)
    {
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order is not in pending status'], 400);
        }

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            
            $order->payment_receipt = '/storage/' . $path;
            $order->status = 'processing';
            $order->save();

            return response()->json([
                'message' => 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.',
                'order' => $order->load('items.product')
            ]);
        }

        return response()->json(['message' => 'File tidak ditemukan'], 400);
    }
}
