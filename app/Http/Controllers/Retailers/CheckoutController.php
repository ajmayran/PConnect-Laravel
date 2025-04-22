<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
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
            // Get cart items with product relationship
            $cartItems = CartDetail::where('cart_id', $cart->id)
                ->with('product')
                ->get();

            // Apply discounts to cart items
            $discountedItems = $this->applyDiscounts($cartItems);

            // Calculate grand total after discounts
            $grandTotal = array_sum(array_column($discountedItems, 'final_subtotal'));

            // Create a custom pagination for discounted items
            $checkoutProducts = $this->paginateCollection(collect($discountedItems), 5);

            // Make sure to pass the distributor_id to the view
            $distributorId = $cart->distributor_id;
        } else {
            $grandTotal = 0;
            $checkoutProducts = $this->paginateCollection(collect([]), 5);
        }

        return view('retailers.checkout.index', compact('checkoutProducts', 'grandTotal', 'user', 'cart', 'distributorId'));
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

            // Get all cart details
            $allCartDetails = CartDetail::whereIn('cart_id', $cartIds)
                ->with('product')
                ->get();

            // Apply discounts
            $discountedItems = $this->applyDiscounts($allCartDetails);

            // Calculate grand total after discounts
            $grandTotal = array_sum(array_column($discountedItems, 'final_subtotal'));

            // Create a custom pagination for discounted items
            $checkoutProducts = $this->paginateCollection(collect($discountedItems), 5);

            // Calculate totals per distributor (after discounts)
            $distributorTotals = [];
            foreach ($carts as $cart) {
                $distributorId = $cart->distributor_id;
                $cartDetailsIds = CartDetail::where('cart_id', $cart->id)->pluck('id')->toArray();

                // Sum the final subtotals for this distributor
                $distributorTotal = 0;
                foreach ($discountedItems as $item) {
                    if (in_array($item['id'], $cartDetailsIds)) {
                        $distributorTotal += $item['final_subtotal'];
                    }
                }

                $distributorTotals[$distributorId] = $distributorTotal;
            }
        } else {
            $grandTotal = 0;
            $checkoutProducts = $this->paginateCollection(collect([]), 5);
            $distributorTotals = [];
        }

        return view('retailers.checkout.all', compact('checkoutProducts', 'grandTotal', 'user', 'distributorTotals', 'carts'));
    }

    /**
     * Create a custom pagination from a collection
     */
    private function paginateCollection($collection, $perPage)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        return new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    private function applyDiscounts($cartItems)
    {
        $discountedItems = [];

        foreach ($cartItems as $item) {
            $product = $item->product;
            $distributorId = $product->distributor_id;
            $quantity = $item->quantity;
            $originalSubtotal = $product->price * $quantity;

            // Find applicable discounts
            $discounts = Discount::where('distributor_id', $distributorId)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->whereHas('products', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->get();

            $discountAmount = 0;
            $freeItems = 0;
            $appliedDiscountName = null;

            // Apply the most favorable discount
            foreach ($discounts as $discount) {
                if ($discount->type === 'percentage') {
                    $potentialDiscount = $discount->calculatePercentageDiscount($product->price) * $quantity;
                    if ($potentialDiscount > $discountAmount) {
                        $discountAmount = $potentialDiscount;
                        $freeItems = 0;
                        $appliedDiscountName = $discount->name;
                    }
                } else if ($discount->type === 'freebie') {
                    $potentialFreeItems = $discount->calculateFreeItems($quantity);
                    // Convert free items to a discount value
                    $potentialDiscount = $potentialFreeItems * $product->price;

                    if ($potentialDiscount > $discountAmount) {
                        $discountAmount = $potentialDiscount;
                        $freeItems = $potentialFreeItems;
                        $appliedDiscountName = $discount->name;
                    }
                }
            }

            // Create a new object with original and discounted values
            $discountedItems[] = [
                'id' => $item->id,
                'product' => $product,
                'quantity' => $quantity,
                'original_price' => $product->price,
                'original_subtotal' => $originalSubtotal,
                'discount_amount' => $discountAmount,
                'free_items' => $freeItems,
                'final_subtotal' => $originalSubtotal - $discountAmount,
                'applied_discount' => $appliedDiscountName,
                'subtotal' => $originalSubtotal - $discountAmount // Adding subtotal for blade template compatibility
            ];
        }

        return $discountedItems;
    }
}
