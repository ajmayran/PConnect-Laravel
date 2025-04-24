<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Stock;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReturnRequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ReturnRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;
        $search = $request->search;

        // Get pending exchange return requests
        $pendingExchangeQuery = ReturnRequest::with(['order', 'retailer', 'items.orderDetail.product'])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->where('status', 'pending')
            ->where('preferred_solution', 'exchange')
            ->latest();

        // Get pending refund return requests
        $pendingRefundQuery = ReturnRequest::with(['order', 'retailer', 'items.orderDetail.product'])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->where('status', 'pending')
            ->where('preferred_solution', 'refund')
            ->latest();

        // Get completed return requests
        $completedReturnsQuery = ReturnRequest::with(['order', 'retailer', 'items.orderDetail.product'])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->whereIn('status', ['approved', 'rejected'])
            ->latest();

        // Apply search if provided
        if ($search) {
            $pendingExchangeQuery->whereHas('order', function ($query) use ($search) {
                $query->where('order_id', 'like', "%{$search}%");
            })
                ->orWhereHas('retailer', function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });

            $pendingRefundQuery->whereHas('order', function ($query) use ($search) {
                $query->where('order_id', 'like', "%{$search}%");
            })
                ->orWhereHas('retailer', function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });

            $completedReturnsQuery->whereHas('order', function ($query) use ($search) {
                $query->where('order_id', 'like', "%{$search}%");
            })
                ->orWhereHas('retailer', function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });
        }

        $pendingExchanges = $pendingExchangeQuery->paginate(10);
        $pendingRefunds = $pendingRefundQuery->paginate(10);
        $completedReturns = $completedReturnsQuery->paginate(10);

        // Preserve query string parameters in pagination links
        if ($request->has('search')) {
            $pendingExchanges->appends(['search' => $request->search]);
            $pendingRefunds->appends(['search' => $request->search]);
            $completedReturns->appends(['search' => $request->search]);
        }

        return view('distributors.returns.index', compact('pendingExchanges', 'pendingRefunds', 'completedReturns'));
    }

    public function show($id)
    {
        $distributorId = Auth::user()->distributor->id;

        // Get return request with related data
        $return = ReturnRequest::with([
            'order',
            'retailer',
            'items.orderDetail',
            'items.orderDetail.product'
        ])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->findOrFail($id);

        // Transform the items data to include product information and ensure orderDetail is loaded
        $return->items->transform(function ($item) {
            $item->product = $item->orderDetail->product;
            return $item;
        });

        // Include the formatted order ID in the response
        $return->order->formatted_order_id = $return->order->formatted_order_id;

        return response()->json([
            'success' => true,
            'return' => $return
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $distributorId = Auth::user()->distributor->id;

        try {
            DB::beginTransaction();

            // Get return request and verify ownership
            $returnRequest = ReturnRequest::whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })->findOrFail($id);

            // Check if return request is still pending
            if ($returnRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This return request has already been processed.'
                ], 400);
            }

            // Update return request
            $returnRequest->update([
                'status' => 'rejected',
                'reject_reason' => $request->rejection_reason,
                'processed_at' => now()
            ]);

            // Update order status back to completed if it was marked as returned
            if ($returnRequest->order->status === 'returned') {
                $returnRequest->order->update([
                    'status' => 'completed'
                ]);
            }

            // Send notification to retailer
            $this->notificationService->create(
                $returnRequest->retailer_id,
                'return_rejected',
                [
                    'title' => 'Return Request Rejected',
                    'message' => "Your return request for order #{$returnRequest->order->formatted_order_id} has been rejected.",
                    'reason' => $request->rejection_reason,
                    'order_id' => $returnRequest->order_id,
                    'recipient_type' => 'retailer'
                ],
                $returnRequest->order_id
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return request rejection failed: ' . $e->getMessage());
        }
        return redirect()->back()->with('success', 'Return request has been rejected successfully.');
    }

    public function checkReturnRequestStatus($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'can_request_again' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Get all return requests for this order
        $returnRequests = ReturnRequest::where('order_id', $orderId)->get();

        // Check if there are any pending return requests
        $hasPendingRequest = $returnRequests->contains(function ($request) {
            return $request->status === 'pending';
        });

        // Check if there are any refunds in the "processing" state for this order
        $hasProcessingRefund = Refund::whereIn('return_request_id', $returnRequests->pluck('id'))
            ->where(function ($query) {
                // Check for refunds with any status
                return $query;
            })
            ->exists();

        // If there are no pending requests and no processing refunds, the user can submit another return request
        return response()->json([
            'can_request_again' => !$hasPendingRequest && !$hasProcessingRefund,
            'message' => $hasPendingRequest
                ? 'There is already a pending return request for this order.'
                : ($hasProcessingRefund
                    ? 'There is a refund currently being processed for this order.'
                    : 'You can submit a new return request.')
        ]);
    }

    public function getReturnItems($returnId)
    {
        $returnRequest = ReturnRequest::with('items.orderDetail.product')
            ->find($returnId);

        if (!$returnRequest) {
            return response()->json(['success' => false, 'message' => 'Return request not found'], 404);
        }

        return response()->json(['success' => true, 'items' => $returnRequest->items]);
    }

    public function getProofImages($returnId)
    {
        // Find the return request by ID
        $returnRequest = ReturnRequest::find($returnId);

        if (!$returnRequest) {
            return response()->json(['success' => false, 'message' => 'Return request not found'], 404);
        }

        // Check if proof_image is a single image path
        $proofImage = $returnRequest->proof_image;

        if (!$proofImage) {
            return response()->json(['success' => true, 'proofImages' => []]); // No proof image provided
        }

        // Return the proof image as an array for consistency
        return response()->json(['success' => true, 'proofImages' => [['url' => asset('storage/' . $proofImage)]]]);
    }


    public function approve(Request $request, $id)
    {
        $distributorId = Auth::user()->distributor->id;

        try {
            DB::beginTransaction();

            // Get return request and verify ownership
            $returnRequest = ReturnRequest::whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
                ->with(['order', 'items.orderDetail.product'])
                ->findOrFail($id);

            // Check if return request is still pending
            if ($returnRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'This return request has already been processed.');
            }

            // Process differently based on return type (refund or exchange)
            $refundAmount = null;
            if ($returnRequest->preferred_solution === 'refund') {
                // Handle refund process and get the amount
                $refundAmount = $this->processRefundApproval($returnRequest);
            } else {
                // Handle exchange process
                $this->processExchangeApproval($returnRequest);
            }

            // Update return request status
            $returnRequest->update([
                'status' => 'approved',
                'processed_at' => now()
            ]);

            // Send notification to retailer
            $messageSuffix = $returnRequest->preferred_solution === 'refund'
                ? "Refund amount: ₱" . number_format($refundAmount, 2)
                : "Please check your notifications for exchange delivery details.";

            $this->notificationService->create(
                $returnRequest->retailer_id,
                'return_approved',
                [
                    'title' => 'Return Request Approved',
                    'message' => "Your {$returnRequest->preferred_solution} request for order #{$returnRequest->order->formatted_order_id} has been approved. {$messageSuffix}",
                    'order_id' => $returnRequest->order_id,
                    'return_type' => $returnRequest->preferred_solution,
                    'recipient_type' => 'retailer'
                ],
                $returnRequest->order_id
            );

            DB::commit();

            $successMessage = 'Return request approved successfully. ' .
                ($returnRequest->preferred_solution === 'refund' ? 'Refund process initiated.' : 'Exchange process initiated.');

            // Redirect back with success message
            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return request approval failed: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while approving the return request: ' . $e->getMessage());
        }
    }

    private function processRefundApproval(ReturnRequest $returnRequest)
    {
        // Calculate refund amount
        $refundAmount = 0;

        foreach ($returnRequest->items as $item) {
            $refundAmount += $item->orderDetail->price * $item->quantity;
        }

        // Create refund record
        $refund = Refund::create([
            'return_request_id' => $returnRequest->id,
            'order_id' => $returnRequest->order_id,
            'amount' => $refundAmount,
            'status' => 'processing', // Initial status
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Instead of adding the amount to the model, store it separately
        // and pass it to where it's needed
        return $refundAmount;
    }

    private function processExchangeApproval(ReturnRequest $returnRequest)
    {
        // For exchanges, we need to:
        // Create new product exchange entries
        // Generate delivery for replacement item

        $exchangeItems = [];

        foreach ($returnRequest->items as $item) {
            // Adjust stock for returned items (same as refund)
            $exchangeItems[] = [
                'product_id' => $item->orderDetail->product_id,
                'quantity' => $item->quantity,
            ];
        }

        // Create an exchange delivery entry
        $delivery = \App\Models\Delivery::create([
            'order_id' => $returnRequest->order_id,
            'tracking_number' => 'EXC-' . strtoupper(uniqid()),
            'status' => 'pending', // Pending delivery status
            'exchange_for_return_id' => $returnRequest->id,
            'notes' => 'Exchange items for return request #' . $returnRequest->id,
            'created_at' => now(),
        ]);

        // Create exchange items inventory adjustment 
        // We'll need to reduce inventory for outgoing exchange items
        foreach ($exchangeItems as $item) {
            $product = \App\Models\Product::find($item['product_id']);

            if ($product->isBatchManaged()) {
                $this->adjustBatchStockForExchange($product, $item['quantity'], $returnRequest);
            } else {
                // Create a stock-out record for regular products
                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => null,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'user_id' => Auth::id(),
                    'notes' => "Exchange for Return Request #{$returnRequest->id}",
                    'stock_updated_at' => now(),
                ]);

                // Update product stock quantity
                $product->stock_quantity -= $item['quantity'];
                $product->save();
            }
        }

        Log::info('Exchange delivery created for return request', [
            'return_id' => $returnRequest->id,
            'delivery_id' => $delivery->id,
            'items_count' => count($exchangeItems)
        ]);
    }

    private function adjustBatchStockForExchange($product, $quantity, $returnRequest)
    {
        $remainingQuantity = $quantity;

        // Get batches ordered by expiry date
        $batches = $product->batches()
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        foreach ($batches as $batch) {
            if ($remainingQuantity <= 0) break;

            $quantityToDeduct = min($batch->quantity, $remainingQuantity);

            // Update batch quantity
            $batch->quantity -= $quantityToDeduct;
            $batch->save();

            // Create stock-out record
            Stock::create([
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'type' => 'out',
                'quantity' => $quantityToDeduct,
                'user_id' => Auth::id(),
                'notes' => "Exchange for Return Request #{$returnRequest->id}",
                'stock_updated_at' => now(),
            ]);

            $remainingQuantity -= $quantityToDeduct;
        }

        // Update product's total stock quantity
        $product->update(['stock_updated_at' => now()]);
    }

    private function adjustProductStock($item, $returnType)
    {
        $product = $item->orderDetail->product;
        $returnRequest = $item->returnRequest;

        if ($product->isBatchManaged()) {
            // First check for soft-deleted batches related to this order
            $deletedBatches = \App\Models\ProductBatch::withTrashed()
                ->where('product_id', $product->id)
                ->whereNotNull('deleted_at')
                ->orderBy('expiry_date')
                ->get();

            $remainingQuantity = $item->quantity;
            $restoredDeletedBatch = false;

            // Try to restore deleted batches first
            foreach ($deletedBatches as $deletedBatch) {
                if ($remainingQuantity <= 0) break;

                // Find stock record related to this batch for this order
                $originalBatchQuantity = \App\Models\Stock::where('batch_id', $deletedBatch->id)
                    ->where('type', 'out')
                    ->where('notes', 'like', '%Order ' . $returnRequest->order->formatted_order_id . '%')
                    ->sum('quantity');

                // If we can't find the original quantity, make a reasonable estimate
                if ($originalBatchQuantity <= 0) {
                    $originalBatchQuantity = min($remainingQuantity, 25); // Use a reasonable default
                    Log::info('Could not determine original batch quantity for return, using estimate', [
                        'estimated_quantity' => $originalBatchQuantity
                    ]);
                }

                // Restore the batch
                $deletedBatch->restore();

                // Use either the original quantity or remaining quantity, whichever is smaller
                $quantityToRestore = min($originalBatchQuantity, $remainingQuantity);
                $deletedBatch->quantity = $quantityToRestore;
                $deletedBatch->save();

                // Create stock record for the restoration
                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => $deletedBatch->id,
                    'type' => 'in',
                    'quantity' => $quantityToRestore,
                    'user_id' => Auth::id(),
                    'notes' => "Return Request #{$returnRequest->id} for Order #{$returnRequest->order->formatted_order_id} approved - {$returnType}",
                    'stock_updated_at' => now()
                ]);

                $remainingQuantity -= $quantityToRestore;
                $restoredDeletedBatch = true;
            }

            // Process remaining quantity using existing or new batches
            if ($remainingQuantity > 0) {
                $this->processRemainingQuantity($product, $remainingQuantity, $returnRequest, $returnType);
            }
        } else {
            // Handle non-batch products
            Stock::create([
                'product_id' => $product->id,
                'batch_id' => null,
                'type' => 'in',
                'quantity' => $item->quantity,
                'user_id' => Auth::id(),
                'notes' => "Return Request #{$returnRequest->id} for Order #{$returnRequest->order->formatted_order_id} approved - {$returnType}",
                'stock_updated_at' => now(),
            ]);

            // Update product stock quantity
            $product->stock_quantity += $item->quantity;
            $product->save();
        }

        // Update product's stock_updated_at timestamp
        $product->update(['stock_updated_at' => now()]);
    }

    private function processRemainingQuantity($product, $remainingQuantity, $returnRequest, $returnType)
    {
        // Handle batch-managed products using existing batches
        $batches = $product->batches()
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        foreach ($batches as $batch) {
            if ($remainingQuantity <= 0) break;

            $quantityToAdd = min($batch->quantity, $remainingQuantity);

            // Update batch quantity
            $batch->quantity += $quantityToAdd;
            $batch->save();

            // Create stock-in record
            Stock::create([
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $quantityToAdd,
                'user_id' => Auth::id(),
                'notes' => "Return Request #{$returnRequest->id} for Order {$returnRequest->order->formatted_order_id} approved - {$returnType}",
                'stock_updated_at' => now(),
            ]);

            $remainingQuantity -= $quantityToAdd;
        }

        // If remaining quantity exists, create a new batch
        if ($remainingQuantity > 0) {
            $newBatch = $product->batches()->create([
                'batch_number' => 'new-' . uniqid(),
                'quantity' => $remainingQuantity,
                'expiry_date' => now()->addMonths(6), // Default expiry date
                'received_at' => now(),
            ]);

            Stock::create([
                'product_id' => $product->id,
                'batch_id' => $newBatch->id,
                'type' => 'in',
                'quantity' => $remainingQuantity,
                'user_id' => Auth::id(),
                'notes' => "Return Request #{$returnRequest->id} for Order {$returnRequest->order->formatted_order_id} approved - {$returnType}",
                'stock_updated_at' => now(),
            ]);
        }
    }
}
