<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductDescController extends Controller
{
    public function show(Product $product)
    {
        // Load the product with its relationships
        $product->load(['distributor', 'category']);

        // Get related products from the same category and distributor
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('distributor_id', $product->distributor_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('retailers.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}
