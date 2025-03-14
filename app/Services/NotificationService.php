<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Notification;
use App\Events\NewNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Create a new notification
     *
     * @param int|null $userId The user to notify (null to use authenticated user)
     * @param string $type Notification type (e.g., 'order_status', 'message', etc.)
     * @param array $data Additional data for the notification
     * @param int|null $relatedId Related model ID (order, delivery, etc.)
     * @return Notification|null
     */
    public function create($userId = null, $type, array $data, $relatedId = null)
    {
        // If no userId provided, try to use authenticated user
        if (is_null($userId)) {
            if (Auth::check()) {
                $userId = Auth::id();
                Log::info('No user ID provided, using authenticated user', ['user_id' => $userId]);
            } else {
                Log::error('Failed to create notification: No user ID provided and no authenticated user');
                return null;
            }
        }


        // Validate userId is numeric
        if (!is_numeric($userId)) {
            Log::error('Failed to create notification: Invalid user ID format', ['user_id' => $userId]);
            return null;
        }

        Log::info('Starting notification creation', [
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
            'related_id' => $relatedId
        ]);

        try {
            // Check if user exists
            $user = User::find($userId);
            if (!$user) {
                Log::error("Failed to create notification: User ID {$userId} not found");
                return null;
            }

            // Create notification
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'data' => $data,
                'is_read' => false,
                'related_id' => $relatedId
            ]);

            Log::info('Notification created successfully', ['notification_id' => $notification->id]);

            // Broadcast notification if available
            try {
                event(new NewNotification($notification));
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast notification: ' . $e->getMessage());
                // Continue execution - notification is created even if broadcast fails
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    /**
     * Create order status notifications
     *
     * @param int $orderId
     * @param string $status New order status
     * @param int $retailerId User ID of the retailer
     * @param int $distributorId User ID of the distributor
     * @param string|null $reason Optional reason for status change (for rejection, cancellation)
     * @return void
     */
    public function orderStatusChanged($orderId, $status, $retailerId, $distributorId, $reason = null)
    {
        // Get the order to use its formatted ID attribute
        $order = Order::find($orderId);
        $formattedOrderId = $order ? $order->formatted_order_id : 'ORD-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);


        // Get retailer and distributor details
        $retailer = \App\Models\User::find($retailerId);
        $distributor = \App\Models\User::find($distributorId);
        $retailerName = $retailer ? $retailer->first_name . ' ' . $retailer->last_name : 'A retailer';
        $distributorName = $distributor ? $distributor->first_name . ' ' . $distributor->last_name : 'A distributor';

        // Define status-specific messages and titles for retailers
        $retailerMessages = [
            'processing' => [
                'title' => 'Order Accepted',
                'message' => "Your order {$formattedOrderId} has been accepted and is being processed."
            ],
            'rejected' => [
                'title' => 'Order Rejected',
                'message' => "Your order {$formattedOrderId} has been rejected." . ($reason ? " Reason: $reason" : "")
            ],
            'completed' => [
                'title' => 'Order Completed',
                'message' => "Your order {$formattedOrderId} has been completed."
            ],
            'cancelled' => [
                'title' => 'Order Cancelled',
                'message' => "Your order {$formattedOrderId} has been cancelled."
            ],
            'returned' => [
                'title' => 'Order Returned',    
                'message' => "Your order {$formattedOrderId} return has been processed."
            ],
        ];

        // Define status-specific messages and titles for distributors
        $distributorMessages = [
            'cancelled' => [
                'title' => 'Order Cancelled',
                'message' => "Order {$formattedOrderId} from {$retailerName} has been cancelled."
            ],
            'returned' => [
                'title' => 'Order Returned',
                'message' => "Order {$formattedOrderId} from {$retailerName} has been returned."
            ],
        ];

        // Default message if status isn't specifically defined
        $defaultMessage = [
            'title' => 'Order Status Updated',
            'message' => "Order {$formattedOrderId} status has been changed to {$status}."
        ];

        // Get the retailer message or use default
        $retailerMessage = $retailerMessages[$status] ?? $defaultMessage;

        // Add order data
        $retailerData = array_merge($retailerMessage, [
            'order_id' => $orderId,
            'status' => $status
        ]);

        // Create retailer notification for all status changes
        $this->create(
            $retailerId,
            'order_status',
            $retailerData,
            $orderId
        );

        // Only notify distributors for specific status changes (cancelled & returned)
        if (isset($distributorMessages[$status])) {
            $distributorData = array_merge($distributorMessages[$status], [
                'order_id' => $orderId,
                'status' => $status
            ]);

            $this->create(
                $distributorId,
                'order_status',
                $distributorData,
                $orderId
            );
        }
    }

    /**
     * Create delivery status notifications
     *
     * @param int $deliveryId
     * @param string $status New delivery status
     * @param int $retailerId User ID of the retailer
     * @return void
     */
    public function deliveryStatusChanged($deliveryId, $status, $retailerId)
    {
        $statusMessages = [
            'out_for_delivery' => 'Your order is out for delivery.',
            'delivered' => 'Your order has been delivered.',
            'failed' => 'Delivery attempt failed.',
        ];

        if (isset($statusMessages[$status])) {
            $data = [
                'title' => 'Delivery Update',
                'message' => $statusMessages[$status],
                'delivery_id' => $deliveryId,
                'status' => $status
            ];

            $this->create($retailerId, 'delivery_update', $data, $deliveryId);
        }
    }

    /**
     * Create payment notifications
     *
     * @param int $paymentId
     * @param string $status Payment status
     * @param int $retailerId User ID of the retailer
     * @param int $distributorId User ID of the distributor
     * @return void
     */
    public function paymentStatusChanged($paymentId, $status, $retailerId, $distributorId)
    {
        // Notify retailer
        $retailerData = [
            'title' => 'Payment Update',
            'message' => "Your payment has been marked as $status.",
            'payment_id' => $paymentId,
            'status' => $status
        ];

        $this->create($retailerId, 'payment_update', $retailerData, $paymentId);

        // Notify distributor
        $distributorData = [
            'title' => 'Payment Update',
            'message' => "A payment has been marked as $status.",
            'payment_id' => $paymentId,
            'status' => $status
        ];

        $this->create($distributorId, 'payment_update', $distributorData, $paymentId);
    }

    /**
     * Create a new product notification for retailers
     * 
     * @param int $productId
     * @param string $productName
     * @param array $retailerIds Array of retailer user IDs
     * @return void
     */
    public function newProductAvailable($productId, $productName, array $retailerIds)
    {
        $data = [
            'title' => 'New Product Available',
            'message' => "A new product \"$productName\" is now available.",
            'product_id' => $productId
        ];

        foreach ($retailerIds as $retailerId) {
            $this->create($retailerId, 'new_product', $data, $productId);
        }
    }

    /**
     * Create a custom notification
     * 
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param string $type
     * @param int|null $relatedId
     * @return Notification
     */
    public function sendCustomNotification($userId, $title, $message, $type = 'general', $relatedId = null)
    {
        $data = [
            'title' => $title,
            'message' => $message
        ];

        return $this->create($userId, $type, $data, $relatedId);
    }

    /**
     * Bulk send a notification to multiple users
     * 
     * @param array $userIds
     * @param string $title
     * @param string $message
     * @param string $type
     * @param int|null $relatedId
     * @return array
     */
    public function sendBulkNotification(array $userIds, $title, $message, $type = 'general', $relatedId = null)
    {
        $data = [
            'title' => $title,
            'message' => $message
        ];

        $notifications = [];

        foreach ($userIds as $userId) {
            $notifications[] = $this->create($userId, $type, $data, $relatedId);
        }

        return $notifications;
    }

    /**
     * Mark a notification as read
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread notifications count for a user
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get latest notifications for a user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatestNotifications($userId, $limit = 5)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }



    /**
     * Create a new order notification
     *
     * @param int $orderId
     * @param int $retailerId
     * @param int $distributorId
     * @return void
     */
    public function newOrderNotification($orderId, $retailerId, $distributorId)
    {
        try {
            // Log the attempt
            Log::info("Creating new order notification for order ID: $orderId, retailer ID: $retailerId, distributor ID: $distributorId");

            // Get the order to use its formatted ID attribute
            $order = Order::find($orderId);
            $formattedOrderId = $order ? $order->formatted_order_id : 'ORD-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);

            // Get retailer details
            $retailer = User::find($retailerId);
            $retailerName = $retailer ? $retailer->first_name . ' ' . $retailer->last_name : 'A retailer';

            // Get the user_id associated with this distributor from the distributors table
            $distributor = \App\Models\Distributors::where('id', $distributorId)->first();

            if (!$distributor) {
                Log::error("Cannot create notification: Distributor ID $distributorId not found in database");
                return null;
            }

            $distributorUserId = $distributor->user_id;

            if (!$distributorUserId) {
                Log::error("Cannot create notification: No user_id associated with distributor ID $distributorId");
                return null;
            }

            // Data for the distributor notification
            $distributorData = [
                'title' => 'New Order Received',
                'message' => "New order {$formattedOrderId} received from {$retailerName}",
                'order_id' => $orderId
            ];

            // Create and broadcast the notification using the correct user ID
            $notification = $this->create($distributorUserId, 'new_order', $distributorData, $orderId);

            Log::info("Notification created successfully", [
                'notification_id' => $notification->id ?? 'unknown',
                'distributor_id' => $distributorId,
                'distributor_user_id' => $distributorUserId
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error("Error in newOrderNotification: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
