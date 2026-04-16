<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json($cartItems);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => $request->quantity,
            ]
        );

        return response()->json($cartItem);
    }

    public function destroy($id, Request $request)
    {
        $cartItem = Cart::where('user_id', $request->user()->id)->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
