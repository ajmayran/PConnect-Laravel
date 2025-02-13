<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class DistributorProductController extends Controller
{
    public function index(): RedirectResponse|View
    {
        $user = Auth::user();
        $distributor = $user->distributor;

        if (!$distributor) {
            return back()->with('error', 'No distributor profile found.');
        }

        $products = Product::where('distributor_id', $distributor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::all();

        return view('distributors.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
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
                'status' => 'pending', // Set status to pending
            ]);

            Log::info('Product created', ['product_id' => $product->id]);

            return redirect()->route('distributors.products.index')
                ->with('success', 'Product created successfully and is pending approval.');
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
        $product = Product::findOrFail($id);

        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'minimum_purchase_qty' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $product->update([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'minimum_purchase_qty' => $request->minimum_purchase_qty,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = Storage::disk('public')->putFileAs('products', $file, $filename);
            $product->image = $path;
        }

        return redirect()->route('distributors.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('distributors.products.index')->with('success', 'Product deleted successfully.');
    }
}
