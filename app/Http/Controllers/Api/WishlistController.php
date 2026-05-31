<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the user's wishlist.
     */
    public function index(Request $request)
    {
        $wishlists = Wishlist::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json($wishlists);
    }

    /**
     * Toggle wishlist item (add if not exists, remove if exists).
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['message' => 'Removed from wishlist', 'is_wishlisted' => false]);
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return response()->json(['message' => 'Added to wishlist', 'is_wishlisted' => true]);
        }
    }
}
