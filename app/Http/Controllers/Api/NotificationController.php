<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get all notifications for logged in user
    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id());

        if ($request->unread === 'true') {
            $query->where('is_read', false);
        }

        $notifications = $query->latest()->paginate(20);

        $unreadCount = Notification::where('user_id', auth()->id())
                                   ->where('is_read', false)
                                   ->count();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // Mark single notification as read
    public function markRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
                                    ->findOrFail($id);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    // Mark all notifications as read
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    // Get unread count only
    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
                             ->where('is_read', false)
                             ->count();

        return response()->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    // Delete notification
    public function destroy($id)
    {
        $notification = Notification::where('user_id', auth()->id())
                                    ->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
        ]);
    }
}