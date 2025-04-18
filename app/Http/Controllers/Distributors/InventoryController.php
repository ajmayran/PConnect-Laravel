<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Product::where('distributor_id', Auth::user()->distributor->id)
            ->select('id', 'product_name', 'image', 'category_id',   DB::raw('(SELECT MAX(stock_updated_at) FROM stocks WHERE stocks.product_id = products.id) as stock_updated_at'))
            
            ->withCount(['stocks as stock_in' => function ($query) {
                $query->where('type', 'in');
            }])
            ->withCount(['stocks as stock_out' => function ($query) {
                $query->where('type', 'out');
            }])
            ->withCount(['batches as total_batch_quantity' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }]);

        // Add a calculated field for stock quantity
        $query->addSelect(DB::raw('(SELECT COALESCE(SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END), 0) FROM stocks WHERE stocks.product_id = products.id) as quantity'));


        if ($search) {
            $query->where('product_name', 'like', '%' . $search . '%');
        }

        $products = $query->with('category')->orderBy('product_name')
            ->paginate(10);

        // Append search query to pagination links
        if ($search) {
            $products->appends(['search' => $search]);
        }

        return view('distributors.inventory.index', compact('products'));
    }

    public function updateStock(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $isBatchManaged = $product->isBatchManaged();
            $stockType = $request->input('stock_type', 'in');

            if ($isBatchManaged) {
                if ($stockType === 'in') {
                    // For stock in with auto-generated batch number
                    $validated = $request->validate([
                        'quantity' => 'required|integer|min:1',
                        'expiry_date' => 'required|date|after:today',
                        'manufacturing_date' => 'nullable|date|before_or_equal:today',
                        'supplier' => 'nullable|string',
                        'notes' => 'nullable|string',
                    ]);

                    // Create new batch with a temporary number
                    $batch = new ProductBatch([
                        'product_id' => $product->id,
                        'batch_number' => 'new', // Temporary batch number
                        'quantity' => $validated['quantity'],
                        'expiry_date' => $validated['expiry_date'],
                        'manufacturing_date' => $validated['manufacturing_date'] ?? null,
                        'supplier' => $validated['supplier'] ?? null,
                        'notes' => $validated['notes'] ?? null,
                        'received_at' => now()
                    ]);
                    $batch->save();

                    // Resequence all batches based on expiry date
                    $this->resequenceBatchesByExpiryDate($product->id);

                    // Get the updated batch with the correct sequence number
                    $batch = ProductBatch::find($batch->id);

                    // Record stock movement
                    Stock::create([
                        'product_id' => $product->id,
                        'batch_id' => $batch->id,
                        'type' => 'in',
                        'quantity' => $validated['quantity'],
                        'user_id' => Auth::id(),
                        'notes' => $validated['notes'] ?? 'Added stocks',
                        'stock_updated_at' => now()
                    ]);
                } else {
                    // For stock out - validate batch selection
                    $validated = $request->validate([
                        'batch_number' => 'required|string',
                        'quantity' => 'required|integer|min:1',
                        'notes' => 'nullable|string',
                    ]);

                    // Find the specific batch
                    $batch = ProductBatch::where('product_id', $product->id)
                        ->where('batch_number', $validated['batch_number'])
                        ->first();

                    if (!$batch) {
                        throw new \Exception("Batch number {$validated['batch_number']} not found");
                    }

                    if ($batch->quantity < $validated['quantity']) {
                        throw new \Exception("Insufficient stock in batch {$validated['batch_number']}");
                    }

                    // Store batch ID before potentially deleting it
                    $batchId = $batch->id;

                    // Update batch quantity
                    $batch->quantity -= $validated['quantity'];
                    $batch->save();

                    // If batch is now empty, delete it and resequence remaining batches
                    if ($batch->quantity <= 0) {
                        // Delete the empty batch first
                        $batch->delete();

                        // Resequence the remaining batches based on expiry date
                        $this->resequenceBatchesByExpiryDate($product->id);
                    }

                    // Record stock movement
                    Stock::create([
                        'product_id' => $product->id,
                        'batch_id' => $batchId, // Use stored ID to avoid null reference
                        'type' => 'out',
                        'quantity' => $validated['quantity'],
                        'user_id' => Auth::id(),
                        'notes' => $validated['notes'] ?? 'Removed stocks',
                        'stock_updated_at' => now()
                    ]);
                }

                // Update product's stock_updated_at timestamp
                $product->update(['stock_updated_at' => now()]);
            } else {
                // Regular stock update for non-batch products
                $validated = $request->validate([
                    'quantity' => 'required|integer|min:1',
                    'notes' => 'nullable|string',
                ]);

                // Calculate current stock from stocks table instead of accessing non-existent column
                $stockIn = Stock::where('product_id', $product->id)->where('type', 'in')->sum('quantity');
                $stockOut = Stock::where('product_id', $product->id)->where('type', 'out')->sum('quantity');
                $currentStock = $stockIn - $stockOut;

                if ($stockType === 'out' && $currentStock < $validated['quantity']) {
                    throw new \Exception("Cannot remove more than available stock ({$currentStock})");
                }

                // Record stock movement
                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => null,
                    'type' => $stockType,
                    'quantity' => $validated['quantity'],
                    'user_id' => Auth::id(),
                    'notes' => $validated['notes'] ?? ($stockType === 'in' ? 'Added stocks' : 'Removed stocks'),
                    'stock_updated_at' => now()
                ]);

                // Just update the stock_updated_at timestamp
                $product->update(['stock_updated_at' => now()]);
            }

            DB::commit();

            // Calculate the current stock for the response
            $currentStock = $product->isBatchManaged()
                ? $product->batches()->sum('quantity')
                : (Stock::where('product_id', $product->id)->where('type', 'in')->sum('quantity') -
                    Stock::where('product_id', $product->id)->where('type', 'out')->sum('quantity'));

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'current_stock' => $currentStock,
                'last_updated' => now()->format('M d, Y H:i')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBatches($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->isBatchManaged()) {
            return response()->json([
                'success' => false,
                'message' => 'This product does not use batch management'
            ], 400);
        }

        $batches = $product->batches()
            ->orderBy('expiry_date') // Order by expiry date for FIFO stock management
            ->get()
            ->map(function ($batch) {
                $batch->expiry_status = $this->getBatchStatus($batch);
                // Format the expiry date in "Month name, date, year" format
                $batch->formatted_expiry_date = $batch->expiry_date->format('F j, Y');
                return $batch;
            });

        return response()->json([
            'success' => true,
            'batches' => $batches
        ]);
    }


    public function resequenceBatchesByExpiryDate($productId)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($productId);

            // Get all batches ordered by expiry date (soonest expiration first)
            $batches = $product->batches()
                ->orderBy('expiry_date')
                ->get();

            if ($batches->count() > 0) {
                // First, apply a temporary prefix to avoid unique constraint conflicts
                foreach ($batches as $index => $batch) {
                    DB::table('product_batches')
                        ->where('id', $batch->id)
                        ->update(['batch_number' => 'temp_' . ($index + 1)]);
                }

                // Then do the final renumbering with the actual numeric sequence
                // Batch number 1 will be the one expiring soonest
                $counter = 1;
                foreach ($batches as $batch) {
                    DB::table('product_batches')
                        ->where('id', $batch->id)
                        ->update(['batch_number' => (string)$counter]);

                    $counter++;
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    private function getBatchStatus($batch)
    {
        $today = now();
        $expiryDate = $batch->expiry_date;

        if ($today > $expiryDate) {
            return 'expired';
        }

        $daysUntilExpiry = $today->diffInDays($expiryDate);

        if ($daysUntilExpiry <= 30) {
            return 'expiring_soon';
        }

        return 'good';
    }

    public function history(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;

        // Get all products for this distributor for the dropdown
        $products = Product::where('distributor_id', $distributorId)
            ->select('id', 'product_name')
            ->orderBy('product_name')
            ->get();

        $query = Stock::whereHas('product', function ($q) use ($distributorId) {
            $q->where('distributor_id', $distributorId);
        })->with(['product', 'batch', 'user']);

        // Apply filters
        if ($request->has('product') && $request->product) {
            $query->where('product_id', $request->product);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get paginated results
        $stockMovements = $query->orderBy('created_at', 'desc')->paginate(10);

        // Append query parameters to pagination links
        if ($request->has('product') || $request->has('type') || $request->has('date_from') || $request->has('date_to')) {
            $stockMovements->appends($request->only(['product', 'type', 'date_from', 'date_to']));
        }

        return view('distributors.inventory.history', compact('stockMovements', 'products'));
    }
}
