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
                'final_subtotal' => $product->price * $validated['quantity'],
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
    
        // Validate the checkout form - now includes is_multi_address and multi_address fields
        $validated = $request->validate([
            'delivery_option' => 'required_if:is_multi_address,0|in:default,saved',
            'selected_address_id' => 'required_if:delivery_option,saved|exists:addresses,id',
            'is_multi_address' => 'sometimes|boolean',
            'multi_address' => 'required_if:is_multi_address,1|array',
            'multi_address.*.address_id' => 'required_if:is_multi_address,1',
            'multi_address.*.quantity' => 'required_if:is_multi_address,1|integer|min:1',
        ]);
    
        $user = Auth::user();
        $product = Product::findOrFail($directPurchase['product_id']);
    
        // Check stock again to ensure it's still available
        if ($product->stock_quantity < $directPurchase['quantity']) {
            return redirect()->back()->with('error', 'Product is out of stock or insufficient quantity available.');
        }
    
        // Determine if we're using multiple addresses
        $isMultiAddress = $request->has('is_multi_address') && $request->input('is_multi_address') == 1;
        
        // Get default address ID for regular orders or for reference
        $defaultAddressId = null;
        
        if ($request->input('delivery_option') === 'default') {
            if (!$user->retailerProfile) {
                return redirect()->back()->with('error', 'Please complete your profile with a default address.');
            }
            
            $defaultAddress = $user->retailerProfile->defaultAddress;
            if (!$defaultAddress) {
                return redirect()->back()->with('error', 'No default address found. Please add an address in your profile.');
            }
            
            $defaultAddressId = $defaultAddress->id;
        } 
        elseif ($request->input('delivery_option') === 'saved') {
            $defaultAddressId = $request->input('selected_address_id');
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
                'is_multi_address' => $isMultiAddress,
                'address_id' => $isMultiAddress ? null : $defaultAddressId, // Store address_id for non-multiple address orders
            ]);
    
            // Include free items in the quantity
            $totalQuantity = $directPurchase['quantity'] + ($directPurchase['free_items'] ?? 0);
    
            // Create order details with discount information
            $orderDetail = OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $totalQuantity, // Include free items here
                'price' => $directPurchase['price'],
                'subtotal' => $directPurchase['final_subtotal'] ?? $directPurchase['subtotal'],
                'discount_amount' => $directPurchase['discount_amount'] ?? 0,
                'free_items' => $directPurchase['free_items'] ?? 0,
                'applied_discount' => $directPurchase['applied_discount'] ?? null
            ]);
    
            // Handle multi-address delivery assignments if enabled
            if ($isMultiAddress) {
                // Validate the total quantity matches
                $totalAssignedQuantity = 0;
                foreach ($request->input('multi_address') as $addressData) {
                    $totalAssignedQuantity += (int)$addressData['quantity'];
                }
                
                if ($totalAssignedQuantity !== $totalQuantity) {
                    throw new \Exception("Total assigned quantity ({$totalAssignedQuantity}) does not match the order quantity ({$totalQuantity})");
                }
                
                // Store the multi-address data in the database
                foreach ($request->input('multi_address') as $addressData) {
                    // Get the address ID for this assignment
                    $multiAddressId = $addressData['address_id'];
                    if ($multiAddressId === 'default') {
                        $multiAddressId = $defaultAddressId;
                    }
                    
                    // Create OrderItemDelivery record with NULL delivery_id (will be filled when order is accepted)
                    \App\Models\OrderItemDelivery::create([
                        'order_details_id' => $orderDetail->id,
                        'address_id' => $multiAddressId,
                        'quantity' => $addressData['quantity'],
                        'delivery_id' => null, // Will be set when order is accepted and delivery is created
                    ]);
                }
            } else {
                // For non-multi-address orders, create a single OrderItemDelivery entry
                \App\Models\OrderItemDelivery::create([
                    'order_details_id' => $orderDetail->id,
                    'address_id' => $defaultAddressId,
                    'quantity' => $totalQuantity,
                    'delivery_id' => null, // Will be set when order is accepted
                ]);
            }
    
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
