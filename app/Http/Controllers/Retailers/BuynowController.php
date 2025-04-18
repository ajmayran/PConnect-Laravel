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
                'subtotal' => $request->input('subtotal')
            ];
        }

        if (!$directPurchase) {
            Log::error('Direct purchase data missing in placeOrder. Session ID: ' . Session::getId());
            return redirect()->route('retailers.all-product')
                ->with('error', 'No direct purchase information found.');
        }

        Log::info('Direct purchase data found in placeOrder: ', $directPurchase);

        // Validate the checkout form based on your actual form fields
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

        // Create the order 
        $order = Order::create([
            'user_id' => $user->id,
            'distributor_id' => $directPurchase['distributor_id'],
            'total_amount' => $directPurchase['subtotal'],
            'status' => 'pending',
            'status_updated_at' => now(),
        ]);

        // Create order details
        OrderDetails::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $directPurchase['quantity'],
            'price' => $directPurchase['price'],
            'subtotal' => $directPurchase['subtotal'],
            'delivery_address' => $deliveryAddress,
        ]);

        $product->save();

        // Commit the transaction
        DB::commit();

        // Clear the session data
        Session::forget('direct_purchase');

        // Send notification to the distributor about the new order
  
        // Get distributor details
        $distributor = \App\Models\Distributors::find($directPurchase['distributor_id']);

        if ($distributor) {
            // Create a new order notification (fix parameters order)
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
    }
}
