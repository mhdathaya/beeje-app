<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ... existing code ...

/**
 * Get latest notifications since a specific timestamp
 */
public function getLatestNotifications(Request $request)
{
    $validator = Validator::make($request->all(), [
        'last_checked' => 'required|date_format:Y-m-d H:i:s'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()
        ], 400);
    }

    $user = Auth::user();
    $lastChecked = $request->last_checked;
    
    $notifications = $user->notifications()
                          ->where('created_at', '>', $lastChecked)
                          ->orderBy('created_at', 'desc')
                          ->get();
    
    return response()->json([
        'status' => true,
        'data' => $notifications,
        'server_time' => now()->format('Y-m-d H:i:s')
    ]);
}
    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->notifications();
        
        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by read status if provided
        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read);
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate(15);
        
        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->notifications()->where('is_read', false)->count();
        
        return response()->json([
            'status' => true,
            'data' => [
                'unread_count' => $count
            ]
        ]);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->update(['is_read' => true]);
        
        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notifications()->update(['is_read' => true]);
        
        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
    
    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Notification deleted'
        ]);
    }
    
    /**
     * Create a notification (for internal use)
     */
    public static function createNotification($userId, $type, $title, $message, $data = null, $orderId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'order_id' => $orderId,
            'is_read' => false
        ]);
    }
}