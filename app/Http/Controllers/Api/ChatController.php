<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Handle chat request with Ollama.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->message;
        $userId = auth('sanctum')->id();

        // 1. Get Product Context (Only products in stock)
        $products = Product::with('category')->where('stock', '>', 0)->get();
        $context = "You are a gaming gear expert assistant. Here is the product list in our marketplace (All items listed are IN STOCK):\n";
        foreach ($products as $product) {
            $context .= "- {$product->name} ({$product->category->name}): price {$product->price}, stock {$product->stock}. ";
            if ($product->switch_type) $context .= "Switches: {$product->switch_type}. ";
            if ($product->dpi) $context .= "DPI: {$product->dpi}. ";
            if ($product->connectivity) $context .= "Connectivity: {$product->connectivity}. ";
            $context .= "\n";
        }

        // 2. Add Order History Context if logged in
        if ($userId) {
            $orders = Order::with('items.product')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            if ($orders->isNotEmpty()) {
                $context .= "\nThe user has the following recent orders:\n";
                foreach ($orders as $order) {
                    $context .= "- Order #{$order->id} (Status: {$order->status}, Total: {$order->total_price}, Date: {$order->created_at}): ";
                    $itemNames = $order->items->map(fn($item) => "{$item->product->name} (x{$item->quantity})")->implode(', ');
                    $context .= $itemNames . "\n";
                }
            }
        }

        // 3. Add Trends/New Arrivals Context (Refined: explicit pitch for items < 7 days)
        $sevenDaysAgo = now()->subDays(7);
        $newArrivals = Product::where('created_at', '>=', $sevenDaysAgo)->orderBy('created_at', 'desc')->get();
        
        if ($newArrivals->isNotEmpty()) {
            $context .= "\nWe have fresh NEW ARRIVALS (last 7 days). Please prioritize pitching these to the user if relevant:\n";
            foreach ($newArrivals as $item) {
                $context .= "- {$item->name} (Brand new arrival!)\n";
            }
        } else {
            // Fallback to recent 5 if no items in last 7 days
            $recent = Product::orderBy('created_at', 'desc')->limit(5)->get();
            if ($recent->isNotEmpty()) {
                $context .= "\nCheck out these recent additions:\n";
                foreach ($recent as $item) {
                    $context .= "- {$item->name}\n";
                }
            }
        }

        // 4. Add Active Coupons Context
        $coupons = \App\Models\Coupon::where('valid_until', '>', now())
            ->where(function($query) {
                $query->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })->get();

        if ($coupons->isNotEmpty()) {
            $context .= "\nWe have the following ACTIVE COUPONS that you can share with the user if they ask for discounts:\n";
            foreach ($coupons as $coupon) {
                $discountText = $coupon->discount_percentage ? "{$coupon->discount_percentage}% off" : "Rp " . number_format($coupon->discount_amount) . " off";
                $context .= "- Code: '{$coupon->code}' ($discountText)\n";
            }
        }

        $context .= "\nPlease help the user based on this information. Answer in the same language as the user (Indonesian/English).";

        // 5. Call Ollama API
        try {
            $ollamaHost = env('OLLAMA_HOST', 'http://localhost:11434');
            $response = Http::timeout(15)->post("{$ollamaHost}/api/generate", [
                'model' => 'qwen2.5',
                'prompt' => "Context: {$context}\n\nUser: {$userMessage}\nAI:",
                'stream' => false,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'AI Service Unavailable',
                    'message' => 'Layanan AI sedang mengalami gangguan (Ollama API failed). Silakan coba lagi nanti.'
                ], 503);
            }

            $aiResponse = $response->json('response');

            // 6. Save History
            ChatHistory::create([
                'user_id' => $userId,
                'message' => $userMessage,
                'response' => $aiResponse,
            ]);

            return response()->json([
                'message' => $userMessage,
                'response' => $aiResponse,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'error' => 'AI Service Timeout',
                'message' => 'Layanan AI tidak merespon dalam waktu yang ditentukan. Pastikan Ollama sudah berjalan.'
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'AI Chat Error',
                'message' => 'Terjadi kesalahan saat berkomunikasi dengan AI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Health check for Ollama service.
     */
    public function healthCheck()
    {
        try {
            $ollamaHost = env('OLLAMA_HOST', 'http://localhost:11434');
            $response = Http::timeout(5)->get("{$ollamaHost}/api/tags");
            
            if ($response->successful()) {
                return response()->json([
                    'status' => 'ok',
                    'service' => 'Ollama',
                    'message' => 'Layanan AI tersedia.'
                ]);
            }
            
            return response()->json([
                'status' => 'error',
                'service' => 'Ollama',
                'message' => 'Layanan AI tidak merespon dengan benar.'
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'down',
                'service' => 'Ollama',
                'message' => 'Layanan AI tidak dapat dijangkau.'
            ], 503);
        }
    }

    /**
     * Get chat history.
     */
    public function history()
    {
        $userId = auth('sanctum')->id();
        
        if (!$userId) {
            return response()->json([]);
        }

        $history = ChatHistory::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        return response()->json($history);
    }
}
