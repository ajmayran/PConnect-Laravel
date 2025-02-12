<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['details.product'])
            ->where('user_id',Auth::id()) 
            ->first();

        if (!$cart) {
            return redirect()->route('retailers.cart.index')
                ->with('error', 'Your cart is empty');
        }

        return view('retailers.checkout.index', [
            'cart' => $cart,
            'total' => $cart->total
        ]);
    }
}