<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Product::where('distributor_id', Auth::user()->distributor->id)
            ->select('id', 'product_name', 'image', 'stock_quantity', 'stock_updated_at');

        if ($search) {
            $query->where('product_name', 'like', '%' . $search . '%');
        }

        $products = $query->orderBy('product_name')
            ->paginate(10);

        // Append search query to pagination links
        if ($search) {
            $products->appends(['search' => $search]);
        }

        return view('distributors.inventory.index', compact('products'));
    }

    public function updateStock(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'stock_quantity' => 'required|integer|min:0',
            ]);

            $product->update([
                'stock_quantity' => $validated['stock_quantity'],
                'stock_updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'last_updated' => now()->format('M d, Y H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
