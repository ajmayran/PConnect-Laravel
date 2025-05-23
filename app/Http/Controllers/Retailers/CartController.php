<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with([
            'details.product.distributor',
            'details.product.discounts'
        ])
            ->where('user_id', Auth::id())
            ->get();

        $groupedItems = collect();

        foreach ($carts as $cart) {
            if ($cart->details->isNotEmpty()) {
                $groupedItems[$cart->distributor_id] = $cart->details->map(function ($detail) {
                    $product = $detail->product;
                    $quantity = $detail->quantity;

                    // If we have saved discount details, use them
                    if ($detail->discount_amount > 0) {
                        // Ensure subtotal is correct with the applied discount
                        $detail->subtotal = ($product->price * $quantity) - $detail->discount_amount;
                    } else {
                        // Calculate discount fresh - this code path will rarely be used
                        // since discounts are calculated when items are added to cart
                        $discountAmount = 0;
                        $freeItems = 0;
                        $appliedDiscount = null;

                        // Get applicable discounts for this product
                        $discounts = \App\Models\Discount::where('distributor_id', $product->distributor_id)
                            ->where('is_active', true)
                            ->where('start_date', '<=', now())
                            ->where('end_date', '>=', now())
                            ->whereHas('products', function ($query) use ($product) {
                                $query->where('product_id', $product->id);
                            })
                            ->get();

                        // Calculate best discount for current quantity
                        foreach ($discounts as $discount) {
                            if ($discount->type === 'percentage') {
                                $potentialDiscount = $discount->calculatePercentageDiscount($product->price) * $quantity;
                                if ($potentialDiscount > $discountAmount) {
                                    $discountAmount = $potentialDiscount;
                                    $freeItems = 0;
                                    $appliedDiscount = $discount->name;
                                }
                            } else if ($discount->type === 'freebie') {
                                $potentialFreeItems = $discount->calculateFreeItems($quantity);
                                $potentialDiscount = $potentialFreeItems * $product->price;

                                if ($potentialDiscount > $discountAmount) {
                                    $discountAmount = $potentialDiscount;
                                    $freeItems = $potentialFreeItems;
                                    $appliedDiscount = $discount->name;
                                }
                            }
                        }

                        // Update cart detail with calculated discount
                        if ($discountAmount > 0) {
                            $subtotal = $quantity * $product->price;
                            $finalSubtotal = $subtotal - $discountAmount;

                            $detail->update([
                                'discount_amount' => $discountAmount,
                                'free_items' => $freeItems,
                                'subtotal' => $finalSubtotal,
                                'applied_discount' => $appliedDiscount
                            ]);
                        } else {
                            // No discount applicable
                            $detail->subtotal = $product->price * $quantity;
                        }
                    }

                    return $detail;
                });
            }
        }

        return view('retailers.cart.index', [
            'groupedItems' => $groupedItems,
        ]);
    }


    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'price' => 'required|numeric',
                'quantity' => 'required|integer|min:1',
                'minimum_purchase_qty' => 'sometimes|integer|min:1'
            ]);

            $product = Product::findOrFail($validated['product_id']);
            $availableStock = $product->stock_quantity;

            // Validate quantity rules
            $minQty = $validated['minimum_purchase_qty'] ?? $product->minimum_purchase_qty;
            if ($validated['quantity'] < $minQty) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Minimum purchase quantity is {$minQty}"
                ], 422);
            }

            if ($validated['quantity'] > $availableStock) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Only {$availableStock} items available in stock"
                ], 422);
            }

            // Ensure the cart exists for this distributor
            $cart = Cart::where('user_id', Auth::id())
                ->where('distributor_id', $product->distributor_id)
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => Auth::id(),
                    'distributor_id' => $product->distributor_id,
                ]);
            }

            // Check for applicable discounts
            $discountAmount = 0;
            $freeItems = 0;
            $appliedDiscount = null;

            // Get applicable discounts for this product
            $discounts = \App\Models\Discount::where('distributor_id', $product->distributor_id)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->whereHas('products', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->get();

            // Calculate discount for the requested quantity
            foreach ($discounts as $discount) {
                if ($discount->type === 'percentage') {
                    $potentialDiscount = $discount->calculatePercentageDiscount($product->price) * $validated['quantity'];
                    if ($potentialDiscount > $discountAmount) {
                        $discountAmount = $potentialDiscount;
                        $freeItems = 0;
                        $appliedDiscount = $discount->name;
                    }
                } else if ($discount->type === 'freebie') {
                    $potentialFreeItems = $discount->calculateFreeItems($validated['quantity']);
                    $potentialDiscount = $potentialFreeItems * $product->price;

                    if ($potentialDiscount > $discountAmount) {
                        $discountAmount = $potentialDiscount;
                        $freeItems = $potentialFreeItems;
                        $appliedDiscount = $discount->name;
                    }
                }
            }

            // Check if the exact product exists in the cart
            $cartDetail = CartDetail::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartDetail) {
                $newQuantity = $cartDetail->quantity + $validated['quantity'];

                if ($newQuantity < $product->minimum_purchase_qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Minimum purchase quantity is {$product->minimum_purchase_qty}"
                    ], 422);
                }

                if ($newQuantity > $availableStock) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Only {$availableStock} items available in stock"
                    ], 422);
                }

                // Recalculate discount for the new quantity
                $discountAmount = 0;
                $freeItems = 0;
                $appliedDiscount = null;

                foreach ($discounts as $discount) {
                    if ($discount->type === 'percentage') {
                        $potentialDiscount = $discount->calculatePercentageDiscount($product->price) * $newQuantity;
                        if ($potentialDiscount > $discountAmount) {
                            $discountAmount = $potentialDiscount;
                            $freeItems = 0;
                            $appliedDiscount = $discount->name;
                        }
                    } else if ($discount->type === 'freebie') {
                        $potentialFreeItems = $discount->calculateFreeItems($newQuantity);
                        $potentialDiscount = $potentialFreeItems * $product->price;

                        if ($potentialDiscount > $discountAmount) {
                            $discountAmount = $potentialDiscount;
                            $freeItems = $potentialFreeItems;
                            $appliedDiscount = $discount->name;
                        }
                    }
                }

                $subtotal = $newQuantity * $product->price;
                $finalSubtotal = $subtotal - $discountAmount;

                $cartDetail->update([
                    'quantity' => $newQuantity,
                    'subtotal' => $finalSubtotal,
                    'discount_amount' => $discountAmount,
                    'free_items' => $freeItems,
                    'applied_discount' => $appliedDiscount
                ]);
            } else {
                $subtotal = $validated['quantity'] * $product->price;
                $finalSubtotal = $subtotal - $discountAmount;

                CartDetail::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $validated['quantity'],
                    'subtotal' => $finalSubtotal,
                    'discount_amount' => $discountAmount,
                    'free_items' => $freeItems,
                    'applied_discount' => $appliedDiscount
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Add to cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage()
            ], 500);
        }
    }



    public function removeProduct($itemId)
    {
        try {
            // Retrieve the cart detail for the current user and given item ID
            $cartDetail = \App\Models\CartDetail::whereHas('cart', function ($q) {
                $q->where('user_id', Auth::id());
            })->where('id', $itemId)->firstOrFail();

            $cart = $cartDetail->cart;
            $cartDetail->delete();

            // If no more products in the cart, remove the cart altogether
            if ($cart->details()->count() === 0) {
                $cart->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Product removed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Remove product error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteCart($cartId)
    {
        try {
            // Retrieve the cart for the current user using the provided cart id
            $cart = Cart::where('user_id', Auth::id())->findOrFail($cartId);

            // Delete all cart details
            $cart->details()->delete();
            // Delete the cart itself
            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cart: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updateQuantities(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.cart_detail_id' => 'required|exists:cart_details,id',
                'items.*.quantity' => 'required|integer|min:1'
            ]);

            foreach ($validated['items'] as $item) {
                $cartDetail = CartDetail::findOrFail($item['cart_detail_id']);
                $product = $cartDetail->product;
                $availableStock = $product->stock_quantity;

                // Validate minimum purchase quantity
                if ($item['quantity'] < $product->minimum_purchase_qty) {
                    throw new \Exception("Minimum purchase quantity for {$product->product_name} is {$product->minimum_purchase_qty}");
                }

                // Validate stock availability
                if ($item['quantity'] > $availableStock) {
                    throw new \Exception("Only {$availableStock} items available in stock for {$product->product_name}");
                }

                // Check for applicable discounts
                $discountAmount = 0;
                $freeItems = 0;
                $appliedDiscount = null;

                // Get applicable discounts for this product
                $discounts = \App\Models\Discount::where('distributor_id', $product->distributor_id)
                    ->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->whereHas('products', function ($query) use ($product) {
                        $query->where('product_id', $product->id);
                    })
                    ->get();

                // Calculate discount for the updated quantity
                foreach ($discounts as $discount) {
                    if ($discount->type === 'percentage') {
                        $potentialDiscount = $discount->calculatePercentageDiscount($product->price) * $item['quantity'];
                        if ($potentialDiscount > $discountAmount) {
                            $discountAmount = $potentialDiscount;
                            $freeItems = 0;
                            $appliedDiscount = $discount->name;
                        }
                    } else if ($discount->type === 'freebie') {
                        $potentialFreeItems = $discount->calculateFreeItems($item['quantity']);
                        $potentialDiscount = $potentialFreeItems * $product->price;

                        if ($potentialDiscount > $discountAmount) {
                            $discountAmount = $potentialDiscount;
                            $freeItems = $potentialFreeItems;
                            $appliedDiscount = $discount->name;
                        }
                    }
                }

                $subtotal = $item['quantity'] * $product->price;
                $finalSubtotal = $subtotal - $discountAmount;

                $cartDetail->update([
                    'quantity' => $item['quantity'],
                    'subtotal' => $finalSubtotal,
                    'discount_amount' => $discountAmount,
                    'free_items' => $freeItems,
                    'applied_discount' => $appliedDiscount
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cart quantities updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
