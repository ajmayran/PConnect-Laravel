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
        $latestPriceUpdate = Product::where('distributor_id', $distributor->id)->max('price_updated_at');

        return view('distributors.products.index', compact('products', 'categories', 'latestPriceUpdate'));
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

            // Validate basic info and specifications
            $validatedData = $request->validate([
                'product_name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'image' => 'required|image|max:2048',
                'brand' => 'required|string|max:255',
                'sku' => 'required|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'tags' => 'nullable|string',
            ]);

            // Handle partial save
            if ($request->has('save_product')) {
                // Set default values for required fields
                $validatedData['price'] = 0;
                $validatedData['stock_quantity'] = 0;
                $validatedData['minimum_purchase_qty'] = 1;
                $alertMessage = 'Product saved successfully (partial save).';
            } else {
                // Validate sales information
                $validatedSales = $request->validate([
                    'price' => 'required|numeric|min:0',
                    'stock_quantity' => 'required|integer|min:0',
                    'minimum_purchase_qty' => 'required|integer|min:1',
                ]);
                $validatedData = array_merge($validatedData, $validatedSales);
                $alertMessage = 'Product saved successfully.';
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('products', $file, $filename);
                $validatedData['image'] = $path;
            }

            // Create product
            $validatedData['distributor_id'] = $distributor->id;
            $validatedData['status'] = 'pending';

            $product = Product::create($validatedData);

            Log::info('Product created', ['product_id' => $product->id]);

            return redirect()->route('distributors.products.index')
                ->with('success', $alertMessage);
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create product. Please try again.');
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Verify if the product belongs to the current distributor
            if ($product->distributor_id !== Auth::user()->distributor->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $validatedData = $request->validate([
                'product_name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'brand' => 'required|string|max:255',
                'sku' => 'required|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'tags' => 'nullable|string',
                'minimum_purchase_qty' => 'required|integer|min:1',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('products', $file, $filename);
                $validatedData['image'] = $path;
            }

            $product->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('distributors.products.index')->with('success', 'Product deleted successfully.');
    }

    public function updatePrice(Request $request, $id)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'price' => $validated['price'],
            'price_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Price updated successfully.');
    }
}
