<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RetailerNotifController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('retailers.notifications.index', compact('notifications'));
    }
    
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
    
    public function getLatestNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
    
    public function markAsRead(Request $request)
    {
        $notificationId = $request->notification_id;
        
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($notification) {
            $notification->update(['is_read' => true]);
        }
        
        return back()->with('success', ' notifications marked as read');
    }
    
    public function markAllAsRead(Request $request)
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return back()->with('success', $count . ' notifications marked as read');
    }
}