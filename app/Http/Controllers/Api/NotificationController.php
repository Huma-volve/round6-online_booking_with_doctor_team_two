<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Notifications retrieved successfully',
            'data' => $notifications
        ], 200);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
            'data' => $notification
        ], 200);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->notifications()->where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read'
        ], 200);
    }
}
