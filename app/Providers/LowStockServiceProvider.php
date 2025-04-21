<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Log;

class LowStockServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('components.dist_navbar', function ($view) {
            // Check if the user is authenticated and is a distributor
            if (Auth::check() && Auth::user()->user_type === 'distributor' && !session('restock_alert_dismissed', false)) {
                try {
                    // Ensure the user has a distributor relationship
                    if (!Auth::user()->distributor) {
                        Log::warning('User is missing distributor relationship');
                        $view->with('lowStockCount', 0);
                        return;
                    }

                    $distributorId = Auth::user()->distributor->id;
                    $threshold = 5; // Define the low stock threshold

                    // Calculate low stock for non-batch-managed products
                    $nonBatchLowStockCount = Product::where('distributor_id', $distributorId)
                        ->get()
                        ->filter(function ($product) use ($threshold) {
                            return !$product->isBatchManaged() && $product->stock_quantity <= $threshold;
                        })
                        ->count();

                    // Calculate low stock for batch-managed products
                    $batchLowStockCount = Product::where('distributor_id', $distributorId)
                        ->get()
                        ->filter(function ($product) use ($threshold) {
                            return $product->isBatchManaged() && $product->batches()->sum('quantity') <= $threshold;
                        })
                        ->count();

                    // Total low stock count
                    $lowStockCount = $nonBatchLowStockCount + $batchLowStockCount;

                    // Log for debugging
                    Log::info("Low stock detection - Non-batch: {$nonBatchLowStockCount}, Batch: {$batchLowStockCount}, Total: {$lowStockCount}");

                    // Pass the low stock count to the view
                    $view->with('lowStockCount', $lowStockCount);
                } catch (\Exception $e) {
                    Log::error("Error in LowStockServiceProvider: " . $e->getMessage());
                    $view->with('lowStockCount', 0);
                }
            }
        });
    }
}