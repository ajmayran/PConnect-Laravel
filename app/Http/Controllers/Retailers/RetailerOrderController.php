<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RetailerOrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $cart = Cart::with('details.product')->where('user_id', Auth::user())->firstOrFail();
            
            $order = Order::create([
                'user_id' => Auth::user(),
                'total_amount' => $cart->total,
                'status' => 'pending'
            ]);

            foreach ($cart->details as $detail) {
                $order->orderDetails()->create([
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                    'price' => $detail->product->price,
                    'subtotal' => $detail->quantity * $detail->product->price
                ]);
            }

            // Clear the cart after successful order creation
            $cart->details()->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'redirect' => route('retailers.orders.show', $order)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order'
            ], 500);
        }
    }

    // Add other controller methods as needed...
}