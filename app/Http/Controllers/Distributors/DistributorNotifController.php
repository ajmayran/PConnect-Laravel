<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DistributorNotifController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Process each notification to correct order IDs in messages
        foreach ($notifications as $notification) {
            // Check if this is an order notification with order_id in data
            if (isset($notification->data['order_id'])) {
                $orderId = $notification->data['order_id'];
                $order = Order::find($orderId);

                if ($order) {
                    // Get the whole data array
                    $data = $notification->data;

                    // More precise pattern that works for both #ORD-000042 and #ORD-42
                    $simpleIdPattern = '/#(ORD-\d+)/';

                    if (isset($data['message'])) {
                        // Modify the message in the array
                        $data['message'] = preg_replace(
                            $simpleIdPattern,
                            '#' . $order->formatted_order_id,
                            $data['message']
                        );

                        // Add the formatted_order_id to the notification data
                        $data['formatted_order_id'] = $order->formatted_order_id;

                        // Set the entire data array back to the notification
                        $notification->data = $data;

                        // Save the notification to persist changes
                        $notification->save();
                    }
                }
            }
        }

        return view('distributors.notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function getLatestNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $processedNotifications = [];

        foreach ($notifications as $notification) {
            $processedNotification = $notification->toArray();

            // Check if this is an order notification
            if (isset($notification->data['order_id'])) {
                $orderId = $notification->data['order_id'];
                $order = Order::find($orderId);

                if ($order) {
                    // Get a copy of the data
                    $data = $processedNotification['data'];

                    // Replace any simplified order IDs with the full formatted version
                    $simpleIdPattern = '/#(ORD-\d+)/';

                    if (isset($data['message'])) {
                        $data['message'] = preg_replace(
                            $simpleIdPattern,
                            '#' . $order->formatted_order_id,
                            $data['message']
                        );
                    }

                    // Add formatted_order_id for easy access
                    $data['formatted_order_id'] = $order->formatted_order_id;

                    // Set the modified data back
                    $processedNotification['data'] = $data;
                }
            }

            $processedNotifications[] = $processedNotification;
        }

        return response()->json([
            'notifications' => $processedNotifications
        ]);
    }

    /**
     * Mark notification as read
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(Request $request)
    {
        $notificationId = $request->notification_id;

        $notification = Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return redirect()->back()->with('success', 'Notification marked as read');
        }

        return redirect()->back()->with('error', 'Notification not found');
    }

    /**
     * Mark all notifications as read
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }
}
