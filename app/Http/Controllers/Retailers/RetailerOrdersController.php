<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RetailerOrdersController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['orderDetails.product', 'distributor'])
            ->latest()
            ->get();

        return view('retailers.orders.index', compact('orders'));
    }

    public function placeOrder(Request $request, $distributorId)
    {
        try {
            $user = Auth::user();
            $deliveryAddress = $request->input('delivery_option') === 'default'
                ? $user->retailerProfile->address
                : $request->input('new_delivery_address');

            // Find the cart
            $cart = Cart::where('user_id', $user->id)
                ->where('distributor_id', $distributorId)
                ->first();

            if (!$cart) {
                return redirect()->back()->with('error', 'Cart not found.');
            }

            // Create new order
            $order = Order::create([
                'user_id' => $user->id,
                'distributor_id' => $distributorId,
                'status' => 'pending',
                'payment_status' => 'pending',
                'status_updated_at' => now(),
            ]);

            // Get cart details and create order details
            $cartDetails = CartDetail::where('cart_id', $cart->id)->get();

            foreach ($cartDetails as $cartDetail) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $cartDetail->product_id,
                    'quantity' => $cartDetail->quantity,
                    'subtotal' => $cartDetail->subtotal,
                    'delivery_address' => $deliveryAddress,
                ]);
            }

            // Clear the cart after successful order creation
            $cart->details()->delete();
            $cart->delete();

            return redirect()->route('retailers.orders.index')
                ->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while placing the order: ' . $e->getMessage());
        }
    }


    public function placeOrderAll(Request $request)
    {
        try {
            $user = Auth::user();
            $deliveryAddress = $request->input('delivery_option') === 'default'
                ? $user->retailerProfile->address
                : $request->input('new_delivery_address');

            $carts = $request->input('carts');
            if (!$carts) {
                return redirect()->back()->with('error', 'No items found in cart.');
            }

            $cartDetails = $request->input('cart_details');

            // Group cart items by distributor
            $cartsByDistributor = [];
            foreach ($carts as $cartId) {
                $cart = Cart::find($cartId);
                if (!$cart) {
                    return redirect()->back()->with('error', 'Cart not found.');
                }

                $distributorId = $cart->distributor_id;
                if (!isset($cartsByDistributor[$distributorId])) {
                    $cartsByDistributor[$distributorId] = [];
                }
                $cartsByDistributor[$distributorId][] = $cart;
            }

            // Create one order per distributor
            foreach ($cartsByDistributor as $distributorId => $distributorCarts) {
                // Create single order for this distributor
                $order = Order::create([
                    'user_id' => $user->id,
                    'distributor_id' => $distributorId,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'status_updated_at' => now(),
                ]);

                // Process all cart items for this distributor
                foreach ($distributorCarts as $cart) {
                    if (!isset($cartDetails[$cart->distributor_id])) {
                        continue;
                    }

                    foreach ($cartDetails[$cart->distributor_id] as $cartDetailId) {
                        $cartDetail = CartDetail::find($cartDetailId);
                        if (!$cartDetail) {
                            continue;
                        }

                        OrderDetails::create([
                            'order_id' => $order->id,
                            'product_id' => $cartDetail->product_id,
                            'quantity' => $cartDetail->quantity,
                            'subtotal' => $cartDetail->subtotal,
                            'delivery_address' => $deliveryAddress,
                        ]);
                    }

                    // Clear the processed cart
                    $cart->details()->delete();
                    $cart->delete();
                }
            }

            return redirect()->route('retailers.orders.index')->with('success', 'Orders placed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while placing the order: ' . $e->getMessage());
        }
    }
}
