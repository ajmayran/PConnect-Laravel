<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartDetail;
use App\Models\Distributors;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class RetailerOrdersController extends Controller
{

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
            ->with(['distributor', 'orderDetails.product', 'payment', 'delivery']) // Added delivery relationship
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

    // public function returnOrder(Request $request, Order $order)
    // {
    //     // Check if the order belongs to the authenticated user
    //     if ($order->user_id !== Auth::user()->id) {
    //         return redirect()->back()->with('error', 'You are not authorized to return this order.');
    //     }

    //     // Check if the order status allows return (usually only delivered/completed orders can be returned)
    //     if (!in_array($order->status, ['completed', 'delivered'])) {
    //         return redirect()->back()->with('error', 'This order cannot be returned.');
    //     }

    //     // Validate return reason
    //     $data = $request->validate([
    //         'return_reason' => 'required|string|max:255',
    //     ]);

    //     // Start a transaction to ensure data consistency
    //     DB::beginTransaction();

    //     try {
    //         $order->update([
    //             'status' => 'returned',
    //             'return_reason' => $data['return_reason'],
    //             'status_updated_at' => now()
    //         ]);

    //         // Send notification to both retailer and distributor
    //         $this->notificationService->orderStatusChanged(
    //             $order->id,
    //             'returned',
    //             $order->user_id,
    //             $order->distributor_id,
    //             $data['return_reason']
    //         );

    //         DB::commit();
    //         return redirect()->back()->with('success', 'Return request submitted successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Order return failed: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Failed to submit return request: ' . $e->getMessage());
    //     }
    // }

    public function cancelOrder(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::user()->id) {
            return redirect()->back()->with('error', 'You are not authorized to cancel this order.');
        }

        // Check if the order status allows cancellation (usually only pending orders can be cancelled)
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        // Start a transaction to ensure data consistency
        DB::beginTransaction();

        try {
            $order->update([
                'status' => 'cancelled',
                'status_updated_at' => now()
            ]);

            // Send notification to both retailer and distributor with the correct recipient_type
            $this->notificationService->orderStatusChanged(
                $order->id,
                'cancelled',
                $order->user_id,
                $order->distributor_id
            );

            // Add additional retailer-specific notification with better context
                // $this->notificationService->create(
                //     $order->user_id,
                //     'order_cancelled',
                //     [
                //         'title' => 'Order Cancelled',
                //         'message' => "You have successfully cancelled your order {$order->formatted_order_id}.",
                //         'order_id' => $order->id,
                //         'recipient_type' => 'retailer'
                //     ],
                //     $order->id
                // );

            DB::commit();
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
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

            // Start a transaction
            DB::beginTransaction();

            // Find the cart
            $cart = Cart::where('user_id', $user->id)
                ->where('distributor_id', $distributorId)
                ->first();

            if (!$cart) {
                DB::rollBack();
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
            $totalAmount = 0;

            foreach ($cartDetails as $cartDetail) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $cartDetail->product_id,
                    'quantity' => $cartDetail->quantity,
                    'subtotal' => $cartDetail->subtotal,
                    'price' => $cartDetail->price,
                    'delivery_address' => $deliveryAddress,
                ]);

                // Calculate total amount
                $totalAmount += $cartDetail->subtotal;

                // Update product stock
                $product = Product::find($cartDetail->product_id);
                if ($product) {
                    $product->stock_quantity -= $cartDetail->quantity;
                    $product->save();
                }
            }

            // Update order with total amount
            $order->total_amount = $totalAmount;
            $order->save();

            // Clear the cart after successful order creation
            $cart->details()->delete();
            $cart->delete();

            // Get distributor information for better notification
            $distributor = Distributors::find($distributorId);

            // Send notification to distributor about new order
            $this->notificationService->newOrderNotification(
                $order->id,
                $user->id,
                $distributorId
            );

            // Add notification for retailer
            $distributorName = $distributor ? $distributor->company_name : 'the distributor';
            $this->notificationService->create(
                $user->id,
                'order_placed',
                [
                    'title' => 'Order Placed Successfully',
                    'message' => "Your order has been placed successfully and is awaiting confirmation from {$distributorName}.",
                    'order_id' => $order->id,
                    'recipient_type' => 'retailer'
                ],
                $order->id
            );

            DB::commit();

            return redirect()->route('retailers.orders.index')
                ->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing order: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

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

            // Start a transaction
            DB::beginTransaction();

            // Get all carts for this user
            $carts = Cart::where('user_id', $user->id)->get();

            if ($carts->isEmpty()) {
                DB::rollBack();
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

            $createdOrders = [];

            // Create one order per distributor
            foreach ($cartsByDistributor as $distributorId => $distributorCarts) {
                // Create single order for this distributor
                $order = Order::create([
                    'user_id' => $user->id,
                    'distributor_id' => $distributorId,
                    'status' => 'pending',
                    'status_updated_at' => now(),
                ]);

                $createdOrders[] = $order;
                $totalAmount = 0;

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

                        $totalAmount += $cartDetail->subtotal;

                        // Update product stock
                        $product = Product::find($cartDetail->product_id);
                        if ($product) {
                            $product->stock_quantity -= $cartDetail->quantity;
                            $product->save();
                        }
                    }

                    // Update order total
                    $order->total_amount = $totalAmount;
                    $order->save();

                    // Clear the processed cart
                    $cart->details()->delete();
                    $cart->delete();
                }

                // Get distributor information
                $distributor = Distributors::find($distributorId);

                // Send notification to distributor about new order
                $this->notificationService->newOrderNotification(
                    $order->id,
                    $user->id,
                    $distributorId
                );

                // Add notification for retailer
                $distributorName = $distributor ? $distributor->company_name : 'the distributor';
                $this->notificationService->create(
                    $user->id,
                    'order_placed',
                    [
                        'title' => 'Order Placed Successfully',
                        'message' => "Your order has been placed successfully and is awaiting confirmation from {$distributorName}.",
                        'order_id' => $order->id,
                        'recipient_type' => 'retailer'
                    ],
                    $order->id
                );
            }

            DB::commit();

            return redirect()->route('retailers.orders.index')
                ->with('success', 'Orders placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing multiple orders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while placing the order: ' . $e->getMessage());
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

    public function trackOrder(Request $request)
    {
        $trackingNumber = $request->tracking_number;

        // Find the delivery by tracking number
        $delivery = \App\Models\Delivery::where('tracking_number', $trackingNumber)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id()); // Only allow the user to track their own orders
            })
            ->with(['order', 'order.distributor', 'order.orderDetails.product'])
            ->first();

        if (!$delivery) {
            return back()->with('error', 'No delivery found with this tracking number or it does not belong to your account.');
        }

        return view('retailers.orders.track', compact('delivery'));
    }
}
