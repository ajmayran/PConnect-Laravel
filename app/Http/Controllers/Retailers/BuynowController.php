<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BuynowController extends Controller
{
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
            return redirect()->route('retailers.products.index')
                ->with('error', 'No direct purchase information found.');
        }

        $user = Auth::user();
        $directProduct = Product::with('distributor')->findOrFail($directPurchase['product_id']);

        return view('retailers.direct-purchase.checkout', compact('directPurchase', 'directProduct', 'user'));
    }

    public function placeOrder(Request $request)
    {
        $directPurchase = Session::get('direct_purchase');

        if (!$directPurchase) {
            return redirect()->route('retailers.products.index')
                ->with('error', 'No direct purchase information found.');
        }

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

        // Determine shipping address based on delivery option
        if ($request->input('delivery_option') === 'default') {
            if (!$user->retailerProfile || !$user->retailerProfile->address) {
                return redirect()->back()->with('error', 'No default delivery address found. Please provide a new address.');
            }
            $shippingAddress = $user->retailerProfile->address;
        } else {
            $shippingAddress = $request->input('new_delivery_address');
        }

        DB::beginTransaction();

        try {
            // Create the order 
            $order = \App\Models\Order::create([
                'user_id' => $user->id,
                'distributor_id' => $directPurchase['distributor_id'],
                'total_amount' => $directPurchase['subtotal'],
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'status_updated_at' => now(),
            ]);

            // Create order details
            OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $directPurchase['quantity'],
                'price' => $directPurchase['price'],
                'subtotal' => $directPurchase['subtotal'],
                'delivery_address' => $shippingAddress,
            ]);

            // Update product stock
            $product->stock_quantity -= $directPurchase['quantity'];
            $product->save();

            // Commit the transaction
            DB::commit();

            // Clear the session data
            Session::forget('direct_purchase');

            return redirect()->route('retailers.orders.index')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
