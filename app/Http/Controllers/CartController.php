<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; // Import the Auth facade

class CartController extends Controller
{

    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'You must be logged in to view your cart.');
        }

        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        return view('distributors.carts.index', compact('carts'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->product_id],
            ['quantity' => $request->quantity]
        );

        return redirect()->route('carts.index')->with('success', 'Product added to cart successfully.');
    }

    public function remove($id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cart->delete();
        return redirect()->route('carts.index')->with('success', 'Product removed from cart successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cart->update(['quantity' => $request->quantity]);

        return redirect()->route('carts.index')->with('success', 'Cart updated successfully.');
    }
}
