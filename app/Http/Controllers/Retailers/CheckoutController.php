<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Address\Entities\Barangay;

class CheckoutController extends Controller
{
    public function checkout($distributorId)
    {

        $user = Auth::user();

        if ($user->retailerProfile && $user->retailerProfile->barangay) {
            // Fetch from API or database
            $barangay = DB::table('barangays')->where('code', $user->retailerProfile->barangay)->first();
            if ($barangay) {
                $user->retailerProfile->barangay_name = $barangay->name;
            } else {
                $user->retailerProfile->barangay_name = 'Unknown';
            }
        }
        $cart = Cart::where('user_id', $user->id)->where('distributor_id', $distributorId)->first();

        if ($cart) {
            $grandTotal = CartDetail::where('cart_id', $cart->id)->sum('subtotal');
            $checkoutProducts = CartDetail::where('cart_id', $cart->id)->paginate(5);
        } else {
            $grandTotal = 0;
            $checkoutProducts = collect([])->paginate(5);
        }

        return view('retailers.checkout.index', compact('checkoutProducts', 'grandTotal', 'user', 'cart'));
    }

    public function checkoutAll()
    {
        $user = Auth::user();

        if ($user->retailerProfile && $user->retailerProfile->barangay) {
            // Fetch from API or database
            $barangay = DB::table('barangays')->where('code', $user->retailerProfile->barangay)->first();
            if ($barangay) {
                $user->retailerProfile->barangay_name = $barangay->name;
            } else {
                $user->retailerProfile->barangay_name = 'Unknown';
            }
        }

        $carts = Cart::where('user_id', $user->id)->get();

        // Only try to get cart IDs if there are actually carts
        if ($carts->isNotEmpty()) {
            $cartIds = $carts->pluck('id')->toArray();
            $grandTotal = CartDetail::whereIn('cart_id', $cartIds)->sum('subtotal');
            $checkoutProducts = CartDetail::whereIn('cart_id', $cartIds)->paginate(5);

            // Calculate totals per distributor
            $distributorTotals = [];
            foreach ($carts as $cart) {
                $distributorId = $cart->distributor_id;
                $distributorTotals[$distributorId] = CartDetail::where('cart_id', $cart->id)->sum('subtotal');
            }
        } else {
            $grandTotal = 0;
            $checkoutProducts = collect([])->paginate(5);
            $distributorTotals = [];
        }

        return view('retailers.checkout.all', compact('checkoutProducts', 'grandTotal', 'user', 'distributorTotals', 'carts'));
    }
}
