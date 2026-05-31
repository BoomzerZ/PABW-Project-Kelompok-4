<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        // Get all unread and read notifications
        $notifications = $request->user()->notifications()->get();
        return response()->json($notifications);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }

    /**
     * Helper to create a dummy notification for testing
     */
    public function triggerDummy(Request $request)
    {
        $user = $request->user();
        
        $user->notify(new \App\Notifications\PromoNotification(
            'Promo Flash Sale Ramadan!',
            'Diskon hingga 70% untuk aksesoris gaming pilihan. Cek sekarang sebelum kehabisan!',
            'promo'
        ));

        return response()->json(['message' => 'Dummy notification triggered']);
    }
}
