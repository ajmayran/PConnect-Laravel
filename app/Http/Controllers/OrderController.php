<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart; // Import the Cart model

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return view('distributors.orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $order = Order::create($request->all());

        // Delete the cart items for the user after order creation
        Cart::where('user_id', $request->user_id)->delete();

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->all());
        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Check if the order can be canceled
        if ($order->status !== 'delivered') {
            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Order canceled successfully.');
        }

        return redirect()->route('orders.index')->with('error', 'Cannot cancel a delivered order.');
    }

    public function confirmOrder(Request $request)
    {
        // Validate the request
        $request->validate([
            'total_amount' => 'required|numeric',
            'delivery_address' => 'required|string',
        ]);

        // Create the order
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $request->total_amount,
            'status' => 'pending',
        ]);

        // Delete the cart items for the user
        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('orders.index')->with('success', 'Order placed successfully.');
    }
}
