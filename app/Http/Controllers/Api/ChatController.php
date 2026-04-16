<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
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

        // 1. Get Product Context
        $products = Product::with('category')->get();
        $context = "You are a gaming gear expert assistant. Here is the product list in our marketplace:\n";
        foreach ($products as $product) {
            $context .= "- {$product->name} ({$product->category->name}): price {$product->price}, stock {$product->stock}. ";
            if ($product->switch_type) $context .= "Switches: {$product->switch_type}. ";
            if ($product->dpi) $context .= "DPI: {$product->dpi}. ";
            if ($product->connectivity) $context .= "Connectivity: {$product->connectivity}. ";
            $context .= "\n";
        }
        $context .= "\nPlease help the user based on this information. Answer in the same language as the user (Indonesian/English).";

        // 2. Call Ollama API
        try {
            $response = Http::timeout(60)->post('http://localhost:11434/api/generate', [
                'model' => 'qwen2.5',
                'prompt' => "Context: {$context}\n\nUser: {$userMessage}\nAI:",
                'stream' => false,
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Ollama API failed'], 500);
            }

            $aiResponse = $response->json('response');

            // 3. Save History
            ChatHistory::create([
                'user_id' => $userId,
                'message' => $userMessage,
                'response' => $aiResponse,
            ]);

            return response()->json([
                'message' => $userMessage,
                'response' => $aiResponse,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection to Ollama failed: ' . $e->getMessage()], 500);
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
