<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function checkout($distributorId)
    {
        
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->where('distributor_id', $distributorId)->first();
        $checkoutProducts = CartDetail::where('cart_id', $cart->id)->get();
        $grandTotal = $checkoutProducts->sum('subtotal');

        return view('retailers.checkout.index', compact('checkoutProducts', 'grandTotal', 'user'));
    }

    public function checkoutAll()
    {
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->get();
        $checkoutProducts = CartDetail::whereIn('cart_id', $carts->pluck('id'))->get();
        $grandTotal = $checkoutProducts->sum('subtotal');

        return view('retailers.checkout.all', compact('checkoutProducts', 'grandTotal', 'user'));
    }
}