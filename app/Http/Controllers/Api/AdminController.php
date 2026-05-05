<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * List all coupons (Admin).
     */
    public function listCoupons()
    {
        return response()->json(Coupon::all());
    }

    /**
     * Create a new coupon.
     */
    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $coupon = Coupon::create($validated);

        return response()->json($coupon, 201);
    }

    /**
     * Update a coupon.
     */
    public function updateCoupon(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'string|unique:coupons,code,' . $id,
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $coupon->update($validated);

        return response()->json($coupon);
    }

    /**
     * Delete a coupon.
     */
    public function destroyCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully']);
    }

    /**
     * List all products (Admin).
     */
    public function listProducts()
    {
        return response()->json(Product::with('category')->get());
    }

    /**
     * Create a new product.
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'switch_type' => 'nullable|string',
            'dpi' => 'nullable|integer',
            'connectivity' => 'nullable|string',
            'sensor' => 'nullable|string',
            'weight' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = Storage::disk('public')->url($path);
        }

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * Update a product.
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'category_id' => 'exists:categories,id',
            'image_url' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'switch_type' => 'nullable|string',
            'dpi' => 'nullable|integer',
            'connectivity' => 'nullable|string',
            'sensor' => 'nullable|string',
            'weight' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it was a local file
            if ($product->image_url && str_contains($product->image_url, '/storage/products/')) {
                $oldPath = 'products/' . basename($product->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = Storage::disk('public')->url($path);
        }

        $product->update($validated);

        return response()->json($product);
    }

    /**
     * Upload a product image.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            return response()->json([
                'url' => Storage::disk('public')->url($path),
                'path' => $path
            ]);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }

    /**
     * Delete a product.
     */
    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * List all orders (Admin).
     */
    public function listOrders()
    {
        $orders = Order::with(['user', 'items.product'])->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,completed',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json($order);
    }
}
