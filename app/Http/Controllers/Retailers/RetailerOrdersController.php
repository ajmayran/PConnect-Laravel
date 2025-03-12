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
            ->where('status', 'pending')
            ->with(['orderDetails.product', 'distributor'])
            ->latest()
            ->paginate(3); 

        return view('retailers.orders.index', compact('orders'));
    }

    public function toPay()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'processing')
            ->with(['distributor', 'orderDetails.product','payment'])
            ->whereHas('payment', function ($query) {
                $query->where('payment_status', 'unpaid');
            })
            ->latest()
            ->paginate(3); 

        return view('retailers.orders.to-pay', compact('orders'));
    }

    public function toReceive()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'processing')
            ->whereHas('delivery', function ($query) {
                $query->where('status', 'out_for_delivery');
            })
            ->with(['distributor', 'orderDetails.product', 'delivery'])
            ->latest()
            ->paginate(3); 

        return view('retailers.orders.to-receive', compact('orders'));
    }

    public function completed()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'completed')

            ->with(['distributor', 'orderDetails.product'])
            ->latest()
            ->paginate(3); 

        return view('retailers.orders.completed', compact('orders'));
    }

    public function cancelled()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['cancelled', 'rejected'])
            ->with(['distributor', 'orderDetails.product'])
            ->latest()
            ->paginate(3); 

        return view('retailers.orders.cancelled', compact('orders'));
    }


    public function returned()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['returned'])
            ->with(['distributor', 'orderDetails.product'])
            ->latest()
            ->paginate(3); 

        return view('retailers.orders.returned', compact('orders'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $reason = $request->input('cancel_reason');
        if ($reason === 'other') {
            $reason = $request->input('custom_reason');
        }

        $order->update([
            'status' => 'cancelled',
            'status_updated_at' => now(),
            'cancel_reason' => $reason
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }


    public function placeOrder(Request $request, $distributorId)
    {
        try {

            $request->validate([
                'delivery_option' => 'required|in:default,other',
                'new_delivery_address' => 'nullable|required_if:delivery_option,other|string',
            ]);

            $user = Auth::user();

            if ($request->input('delivery_option') === 'default') {
                // Check if retailer profile and address details exist
                if (!$user->retailerProfile || !$user->retailerProfile->barangay_name) {
                    return redirect()->back()->with('error', 'No default delivery address found. Please provide a new address.');
                }
                // Combine barangay name and street
                $deliveryAddress = $user->retailerProfile->barangay_name .
                    ($user->retailerProfile->street ? ', ' . $user->retailerProfile->street : '');
            } else {
                $deliveryAddress = $request->input('new_delivery_address');
            }

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
                    'price' => $cartDetail->price,
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
            $request->validate([
                'delivery_option' => 'required|in:default,other',
                'new_delivery_address' => 'nullable|required_if:delivery_option,other|string',
            ]);

            $user = Auth::user();

            if ($request->input('delivery_option') === 'default') {
                // Check if retailer profile and address details exist
                if (!$user->retailerProfile || !$user->retailerProfile->barangay_name) {
                    return redirect()->back()->with('error', 'No default delivery address found. Please provide a new address.');
                }
                // Combine barangay name and street
                $deliveryAddress = $user->retailerProfile->barangay_name .
                    ($user->retailerProfile->street ? ', ' . $user->retailerProfile->street : '');
            } else {
                $deliveryAddress = $request->input('new_delivery_address');
            }

            // Get all carts for this user
            $carts = Cart::where('user_id', $user->id)->get();

            if ($carts->isEmpty()) {
                return redirect()->back()->with('error', 'No items found in cart.');
            }

            // Group cart items by distributor
            $cartsByDistributor = [];
            foreach ($carts as $cart) {
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
                    'status' => 'pending',
                    'status_updated_at' => now(),
                ]);

                // Process all cart items for this distributor
                foreach ($distributorCarts as $cart) {
                    // Get all cart details for this cart
                    $allCartDetails = CartDetail::where('cart_id', $cart->id)->get();

                    // Add all cart details to the order
                    foreach ($allCartDetails as $cartDetail) {
                        OrderDetails::create([
                            'order_id' => $order->id,
                            'product_id' => $cartDetail->product_id,
                            'quantity' => $cartDetail->quantity,
                            'price' => $cartDetail->price,
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
    public function myPurchases()
    {
        $orders = Order::with(['orderDetails.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('retailers.profile.my-purchase', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('retailers.orders.show', compact('order'));
    }

    public function getOrderDetails(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['orderDetails.product']);
        return view('retailers.profile.order-details', compact('order'));
    }
}
