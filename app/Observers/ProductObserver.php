<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductHistory;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        ProductHistory::create([
            'product_id' => $product->id,
            'user_id' => Auth::id() ?? 1, // Default to admin if no auth
            'action_type' => 'created',
            'new_values' => $product->toArray(),
            'notes' => 'Product created'
        ]);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $changes = $product->getChanges();
        
        // Skip updating if only timestamps were modified
        if (count($changes) === 1 && array_key_exists('updated_at', $changes)) {
            return;
        }
        
        $original = array_intersect_key($product->getOriginal(), $changes);
        
        if (!empty($changes)) {
            ProductHistory::create([
                'product_id' => $product->id,
                'user_id' => Auth::id() ?? 1,
                'action_type' => 'updated',
                'old_values' => $original,
                'new_values' => $changes,
                'notes' => 'Product updated'
            ]);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        ProductHistory::create([
            'product_id' => $product->id,
            'user_id' => Auth::id() ?? 1,
            'action_type' => 'deleted',
            'old_values' => $product->toArray(),
            'notes' => 'Product deleted'
        ]);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        ProductHistory::create([
            'product_id' => $product->id,
            'user_id' => Auth::id() ?? 1,
            'action_type' => 'restored',
            'new_values' => $product->toArray(),
            'notes' => 'Product restored from deletion'
        ]);
    }
}