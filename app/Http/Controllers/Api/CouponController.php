<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate a coupon code.
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total_price' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json(['message' => 'Coupon is expired or limit reached'], 400);
        }

        $discount = $coupon->calculateDiscount($request->total_price);

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'discount' => $discount,
            'final_price' => $request->total_price - $discount
        ]);
    }
}
