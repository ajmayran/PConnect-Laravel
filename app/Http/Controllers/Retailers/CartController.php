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
    public function 
    index()
    {
        // Get all carts for the current user with their details
        $carts = Cart::with(['details.product.distributor'])
            ->where('user_id', Auth::id())
            ->get();

        // Group cart items by cart (which is already separated by distributor)
        $groupedItems = collect();

        foreach ($carts as $cart) { 
            if ($cart->details->isNotEmpty()) {
                $groupedItems[$cart->distributor_id] = $cart->details;
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
                'price' => 'required|exists:products,price',
                'quantity'   => 'required|integer|min:1',
                'buy_now' => 'sometimes|boolean'
            ]);

            $product = Product::findOrFail($validated['product_id']);

            // Validate minimum purchase quantity
            if ($validated['quantity'] < $product->minimum_purchase_qty) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Minimum purchase quantity is {$product->minimum_purchase_qty}"
                ], 422);
            }

            // Validate stock availability
            if ($validated['quantity'] > $product->stock_quantity) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Only {$product->stock_quantity} items available in stock"
                ], 422);
            }

            // Find or create a cart specific to the product's distributor
            $cart = Cart::firstOrCreate([
                'user_id'        => Auth::id(),
                'distributor_id' => $product->distributor_id,
            ]);

            // Check if the product already exists in the cart (cart detail)
            $cartDetail = CartDetail::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartDetail) {
                // For "Buy Now", replace the quantity instead of adding to it
                if ($request->has('buy_now') && $request->buy_now) {
                    $cartDetail->update([
                        'quantity' => $validated['quantity'],
                        'subtotal' => $validated['quantity'] * $product->price
                    ]);
                } else {
                    // Update the quantity for an existing product in the cart
                    $newQuantity = $cartDetail->quantity + $validated['quantity'];

                    // Make sure the new quantity is still meeting the minimum and stock requirements
                    if ($newQuantity < $product->minimum_purchase_qty) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Minimum purchase quantity is {$product->minimum_purchase_qty}"
                        ], 422);
                    }

                    if ($newQuantity > $product->stock_quantity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Only {$product->stock_quantity} items available in stock"
                        ], 422);
                    }

                    $cartDetail->update([
                        'quantity' => $newQuantity,
                        'subtotal' => $newQuantity * $product->price
                    ]);
                }
            } else {
                // Otherwise, create a new record in cart details for this product
                CartDetail::create([
                    'cart_id'    => $cart->id,
                    'product_id' => $product->id,
                    'price'   => $product->price,
                    'quantity'   => $validated['quantity'],
                    'subtotal'   => $product->price * $validated['quantity']
                ]);
            }

            DB::commit();

            if ($request->has('buy_now') && $request->buy_now) {
                return response()->json([
                    'success' => true,
                    'message' => 'Processing your order...',
                    'distributor_id' => $product->distributor_id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add to cart error: ' . $e->getMessage());
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

                // Validate minimum purchase quantity
                if ($item['quantity'] < $product->minimum_purchase_qty) {
                    throw new \Exception("Minimum purchase quantity for {$product->product_name} is {$product->minimum_purchase_qty}");
                }

                // Validate stock availability
                if ($item['quantity'] > $product->stock_quantity) {
                    throw new \Exception("Only {$product->stock_quantity} items available in stock for {$product->product_name}");
                }

                $cartDetail->update([
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['quantity'] * $product->price
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
