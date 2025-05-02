<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class CheckMultiAddressOrders extends Command
{
    protected $signature = 'orders:check-multi-address';
    protected $description = 'Check multi-address orders and mark as completed if all deliveries are delivered';

    public function __construct(private NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Checking multi-address orders...');
        
        // Find multi-address orders that are not completed yet
        $multiAddressOrders = Order::where('is_multi_address', true)
            ->whereIn('status', ['processing', 'out_for_delivery', 'in_transit']) // Be more specific about statuses
            ->get();
            
        $this->info("Found {$multiAddressOrders->count()} multi-address orders to check");
        $completedCount = 0;
        
        foreach ($multiAddressOrders as $order) {
            // Get all deliveries for this order
            $allOrderDeliveries = Delivery::where('order_id', $order->id)->get();
            
            // Skip if there are no deliveries
            if ($allOrderDeliveries->isEmpty()) {
                $this->info("Order {$order->id} has no deliveries, skipping");
                continue;
            }
            
            $allDelivered = $allOrderDeliveries->every(function ($delivery) {
                return $delivery->status === 'delivered';
            });
            
            if ($allDelivered) {
                // Count how many are paid vs total
                $paidDeliveries = $allOrderDeliveries->filter(function ($delivery) {
                    return $delivery->payment_status === 'paid';
                })->count();
                
                $totalDeliveries = $allOrderDeliveries->count();
                
                // Determine overall payment status
                $orderPaymentStatus = 'unpaid';
                if ($paidDeliveries === $totalDeliveries) {
                    $orderPaymentStatus = 'paid';
                } else if ($paidDeliveries > 0) {
                    $orderPaymentStatus = 'partial';
                }
                
                try {
                    // Update order status to completed
                    $order->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);
                    
                    Log::info('Multi-address order completed - all deliveries delivered:', [
                        'order_id' => $order->id,
                        'total_deliveries' => $totalDeliveries,
                        'paid_deliveries' => $paidDeliveries,
                        'payment_status' => $orderPaymentStatus
                    ]);
                    
                    // Add notification for completed order
                    $this->notificationService->orderStatusChanged(
                        $order->id,
                        'completed',
                        $order->user_id,
                        $order->distributor_id
                    );
                    
                    $this->info("Order {$order->id} marked as completed with payment status: {$orderPaymentStatus}");
                    $completedCount++;
                } catch (\Exception $e) {
                    Log::error("Error updating order {$order->id}: " . $e->getMessage());
                    $this->error("Failed to update order {$order->id}: " . $e->getMessage());
                }
            } else {
                $deliveredCount = $allOrderDeliveries->where('status', 'delivered')->count();
                $this->info("Order {$order->id}: {$deliveredCount}/{$allOrderDeliveries->count()} deliveries completed");
            }
        }
        
        $this->info("Command completed. {$completedCount} orders were marked as completed.");
        return Command::SUCCESS;
    }
}