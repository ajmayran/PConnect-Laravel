<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        try {
            Log::info('Product store attempt', $request->all());

            $user = Auth::user();
            $distributor = $user->distributor;

            if (!$distributor) {
                Log::error('No distributor found for user: ' . $user->id);
                return back()->with('error', 'No distributor profile found for this user.');
            }
            $validatedData = $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
                'product_name' => 'required|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'minimum_purchase_qty' => 'required|integer',
                'category_id' => 'required|exists:categories,id',
            ]);

            Log::info('Validation passed', $validatedData);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('products', $file, $filename);
                $validatedData['image'] = $path;
            }

            $product = Product::create([
                'distributor_id' => $distributor->id,
                'product_name' => $validatedData['product_name'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'stock_quantity' => $validatedData['stock_quantity'],
                'minimum_purchase_qty' => $validatedData['minimum_purchase_qty'],
                'category_id' => $validatedData['category_id'],
                'image' => $validatedData['image'] ?? null,
            ]);

            Log::info('Product created', ['product_id' => $product->id]);

            return redirect()->route('distributors.products.index')
                ->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create product. Please try again.');
        }
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
