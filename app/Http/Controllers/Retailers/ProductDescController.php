<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Product;
use App\Models\Distributors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductDescController extends Controller
{
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $distributor = Distributors::findOrFail($product->distributor_id);

        $relatedProducts = Product::where('distributor_id', $product->distributor_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'accepted')
            ->where('stock_quantity', '>', 0)
            ->limit(5)
            ->get();

        return view('retailers.products.show', compact('product', 'distributor', 'relatedProducts'));
    }
}
