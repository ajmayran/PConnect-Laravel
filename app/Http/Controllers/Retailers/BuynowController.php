<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Session;

class BuynowController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function buyNow(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity'   => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($validated['product_id']);

            // Validate minimum purchase quantity
            if ($validated['quantity'] < $product->minimum_purchase_qty) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum purchase quantity is {$product->minimum_purchase_qty}"
                ], 422);
            }

            // Validate stock availability
            if ($validated['quantity'] > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$product->stock_quantity} items available in stock"
                ], 422);
            }

            // Store purchase data in session
            $directPurchase = [
                'product_id' => $product->id,
                'distributor_id' => $product->distributor_id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $validated['quantity'],
            ];

            // Store in session
            Session::put('direct_purchase', $directPurchase);
            Session::save(); // Ensure session is saved

            Log::info('Direct purchase data stored in session: ', $directPurchase);

            return response()->json([
                'success' => true,
                'message' => 'Processing your order...',
                'redirect_url' => route('retailers.direct-purchase.checkout')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkout()
    {
        $directPurchase = Session::get('direct_purchase');

        if (!$directPurchase) {
            // Log for debugging
            Log::error('Direct purchase data missing in checkout. Session ID: ' . Session::getId());

            return redirect()->route('retailers.all-product')
                ->with('error', 'No direct purchase information found.');
        }

        Log::info('Direct purchase data found in checkout: ', $directPurchase);

        $user = Auth::user();
        $directProduct = Product::with('distributor')->findOrFail($directPurchase['product_id']);

        // Check for applicable discounts
        $discount = \App\Models\Discount::where('distributor_id', $directProduct->distributor_id)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('products', function ($query) use ($directProduct) {
                $query->where('product_id', $directProduct->id);
            })
            ->first();

        // Apply discount if applicable
        $discountAmount = 0;
        $freeItems = 0;
        $appliedDiscount = null;

        if ($discount) {
            if ($discount->type === 'percentage') {
                $discountAmount = ($directProduct->price * $discount->percentage / 100) * $directPurchase['quantity'];
                $appliedDiscount = $discount->name;

                // Update final_subtotal for percentage discount
                $directPurchase['final_subtotal'] = $directPurchase['subtotal'] - $discountAmount;
            } else if ($discount->type === 'freebie' && $discount->buy_quantity > 0) {
                // Calculate free items (buy X get Y free logic)
                $sets = floor($directPurchase['quantity'] / $discount->buy_quantity);
                $freeItems = $sets * $discount->free_quantity;

                // For freebie discounts, don't reduce the price
                $discountAmount = 0; // No price discount
                $appliedDiscount = $discount->name;

                // Keep the original subtotal for freebie discounts
                $directPurchase['final_subtotal'] = $directPurchase['subtotal'];
            }
        }

        // Update the direct purchase with discount info
        $directPurchase['discount_amount'] = $discountAmount;
        $directPurchase['free_items'] = $freeItems;
        $directPurchase['applied_discount'] = $appliedDiscount;

        // Save updated info in session
        Session::put('direct_purchase', $directPurchase);

        return view('retailers.direct-purchase.checkout', compact('directPurchase', 'directProduct', 'user'));
    }

    public function placeOrder(Request $request)
    {
        $directPurchase = Session::get('direct_purchase');

        if (!$directPurchase && $request->has('product_id')) {
            Log::warning('Rebuilding direct purchase from form data. Session ID: ' . Session::getId());
            $directPurchase = [
                'product_id' => $request->input('product_id'),
                'distributor_id' => $request->input('distributor_id'),
                'quantity' => $request->input('quantity'),
                'price' => $request->input('price'),
                'subtotal' => $request->input('subtotal'),
                'discount_amount' => $request->input('discount_amount', 0),
                'free_items' => $request->input('free_items', 0),
                'applied_discount' => $request->input('applied_discount'),
                'final_subtotal' => $request->input('final_subtotal', $request->input('subtotal'))
            ];
        }

        if (!$directPurchase) {
            Log::error('Direct purchase data missing in placeOrder. Session ID: ' . Session::getId());
            return redirect()->route('retailers.all-product')
                ->with('error', 'No direct purchase information found.');
        }

        Log::info('Direct purchase data found in placeOrder: ', $directPurchase);

        // Validate the checkout form
        $validated = $request->validate([
            'delivery_option' => 'required|in:default,other',
            'new_delivery_address' => 'nullable|required_if:delivery_option,other|string',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($directPurchase['product_id']);

        // Check stock again to ensure it's still available
        if ($product->stock_quantity < $directPurchase['quantity']) {
            return redirect()->back()->with('error', 'Product is out of stock or insufficient quantity available.');
        }

        // Determine delivery address based on delivery option
        if ($request->input('delivery_option') === 'default') {
            if (!$user->retailerProfile || (!$user->retailerProfile->barangay && !$user->retailerProfile->street)) {
                return redirect()->back()->with('error', 'No default delivery address found. Please provide a new address.');
            }
            // Format the address properly using the available fields
            $deliveryAddress = $user->retailerProfile->barangay_name . ', ' . ($user->retailerProfile->street ?? '');
        } else {
            $deliveryAddress = $request->input('new_delivery_address');
        }

        DB::beginTransaction();

        try {
            // Create the order - use the final subtotal which includes any discounts
            $order = Order::create([
                'user_id' => $user->id,
                'distributor_id' => $directPurchase['distributor_id'],
                'total_amount' => $directPurchase['final_subtotal'] ?? $directPurchase['subtotal'],
                'status' => 'pending',
                'status_updated_at' => now(),
            ]);

            // Include free items in the quantity
            $totalQuantity = $directPurchase['quantity'] + ($directPurchase['free_items'] ?? 0);

            // Create order details with discount information
            OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $totalQuantity, // Include free items here
                'price' => $directPurchase['price'],
                'subtotal' => $directPurchase['final_subtotal'] ?? $directPurchase['subtotal'],
                'delivery_address' => $deliveryAddress,
                'discount_amount' => $directPurchase['discount_amount'] ?? 0,
                'free_items' => $directPurchase['free_items'] ?? 0,
                'applied_discount' => $directPurchase['applied_discount'] ?? null
            ]);

            $product->save();

            // Commit the transaction
            DB::commit();

            // Clear the session data
            Session::forget('direct_purchase');

            // Send notification to the distributor about the new order
            $distributor = \App\Models\Distributors::find($directPurchase['distributor_id']);

            if ($distributor) {
                // Create a new order notification
                $this->notificationService->newOrderNotification(
                    $order->id,
                    $user->id,
                    $distributor->id
                );

                // Create confirmation notification for retailer
                $this->notificationService->create(
                    $user->id,
                    'order_placed',
                    [
                        'title' => 'Order Placed Successfully',
                        'message' => "Your order has been placed successfully and is awaiting confirmation from {$distributor->company_name}.",
                        'order_id' => $order->id,
                        'recipient_type' => 'retailer'
                    ],
                    $order->id
                );
            }

            return redirect()->route('retailers.orders.index')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing direct purchase order: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
