<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category; // Import the Category model

class ProductController extends Controller
{
public function index()
{
    $products = Product::all();
    $categories = Category::all(); // Fetch categories
    return view('distributors.products.index', compact('products', 'categories'));
}


    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('distributors.products.show', compact('product'));
    }

public function create()
{
    $categories = Category::all(); // Fetch categories
    return view('distributors.products.create', compact('categories'));
}


    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        Product::create([
            'name' => $request->product_name, // Update to match the form field
            'description' => $request->description,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'minimum_purchase_qty' => $request->minimum_purchase_qty,
            'category_id' => $request->category_id,
            'image' => $request->image, // Ensure to handle image upload if necessary
        ]);
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('distributors.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('distributors.products.index')->with('success', 'Product deleted successfully.');
    }
}
