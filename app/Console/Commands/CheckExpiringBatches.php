<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\ProductBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class CheckExpiringBatches extends Command
{
    protected $signature = 'batches:check-expiring';
    protected $description = 'Check and send notifications for expiring product batches';

    /**
     * The notification service instance.
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $daysThreshold = 30; // Configure how many days before expiry to notify
        $today = now();
        $thresholdDate = $today->copy()->addDays($daysThreshold);
        
        $this->info("Checking for expiring batches at {$today->format('Y-m-d H:i:s')}...");
        
        // Get all expiring batches grouped by distributor
        $expiringBatches = ProductBatch::with(['product.distributor.user'])
            ->where('expiry_date', '>', $today)
            ->where('expiry_date', '<=', $thresholdDate)
            ->whereNull('expiry_notification_sent') // Track notifications already sent
            ->get()
            ->groupBy('product.distributor.id');
            
        $count = $expiringBatches->count();
        $this->info("Found {$count} distributor(s) with expiring batches");
        
        foreach ($expiringBatches as $distributorId => $batches) {
            // Get the distributor user ID for sending notification
            $distributorUserId = $batches->first()->product->distributor->user_id;
            
            // Group batches by product for a more organized notification
            $groupedByProduct = $batches->groupBy('product_id');
            
            foreach ($groupedByProduct as $productId => $productBatches) {
                $product = $productBatches->first()->product;
                $batchCount = $productBatches->count();
                $earliestExpiry = $productBatches->min('expiry_date');
                
                $this->line("- Sending notification for {$batchCount} batch(es) of {$product->product_name} to distributor ID: {$distributorUserId}");
                
                // Create notification for each product with expiring batches
                $notification = $this->notificationService->create(
                    $distributorUserId,
                    'expiring_batches',
                    [
                        'title' => 'Expiring Product Batches Alert',
                        'message' => "{$batchCount} batch(es) of {$product->product_name} will expire by " . 
                                  $earliestExpiry->format('M d, Y') . ". Please check your inventory.",
                        'product_id' => $productId,
                        'batch_count' => $batchCount,
                        'earliest_expiry' => $earliestExpiry->format('Y-m-d')
                    ],
                    $productId
                );
                
                if ($notification) {
                    $this->info("  ✓ Notification sent successfully (ID: {$notification->id})");
                    
                    // Mark these batches as notified
                    foreach ($productBatches as $batch) {
                        $batch->update(['expiry_notification_sent' => now()]);
                    }
                } else {
                    $this->error("  ✗ Failed to send notification");
                }
            }
        }
        
        $this->info("Completed checking for expiring batches");
        
        return 0;
    }
}