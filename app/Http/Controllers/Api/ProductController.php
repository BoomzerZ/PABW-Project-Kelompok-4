<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with pagination, search, and sorting.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // 1. Robust Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function($cq) use ($search) {
                      $cq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // 2. Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Price Filter
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 4. Sorting options
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 5. Pagination (default 12 items per page)
        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        $products->each(function($product) {
            $product->append('average_rating');
        });

        return response()->json($products);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'reviews.user'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->append('average_rating');

        return response()->json($product);
    }

    /**
     * Get all products for AI context.
     */
    public function aiContext()
    {
        $products = Product::with('category')->get();
        return response()->json($products);
    }
}
