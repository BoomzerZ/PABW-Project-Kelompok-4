<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $product = Product::findOrFail($id);

        // Optional: Check if user actually bought the product
        // For now, we allow any authenticated user to review

        // Check if user already reviewed
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('product_id', $id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You have already reviewed this product'], 400);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review->load('user')
        ]);
    }
}
