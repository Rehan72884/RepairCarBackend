<?php

namespace App\Http\Controllers\NotificationManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = auth()->user();

        return response()->json([
            'notifications' => $user->notifications,
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $user = auth()->user();
        $notification = $user->notifications()->where('id', $request->notification_id)->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }

    // DELETE /api/notifications/delete
    public function deleteNotification(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $user = auth()->user();
        $notification = $user->notifications()->where('id', $request->notification_id)->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted']);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }
}
