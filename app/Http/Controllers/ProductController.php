<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category; // Import the Category model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $user = Auth::user();
        $distributor = $user->distributor;

        if (!$distributor) {
            return back()->with('error', 'No distributor profile found for this user.');
        }

        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'product_name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'minimum_purchase_qty' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'distributor_id' => 'required'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Store the file in the public disk under products directory
            $path = Storage::disk('public')->putFileAs(
                'products',
                $file,
                $filename
            );

            $validatedData['image'] = $path;
        }


        $validatedData['distributor_id'] = $distributor->id;
        $product = Product::create($validatedData);
        return redirect()->route('distributors.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('distributors.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'product_name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'minimum_purchase_qty' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);


        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('storage/products'), $imageName);

            $validatedData['image'] = 'storage/products/' . $imageName;
        }
        $product->update($request->all());
        return redirect()->route('distributors.products.index')  // Updated route name
            ->with('success', 'Product updated successfully.');
    }
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('distributors.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
