<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DistributorProductController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        $user = Auth::user();
        $distributor = $user->distributor;

        if (!$distributor) {
            return back()->with('error', 'No distributor profile found.');
        }

        $query = Product::where('distributor_id', $distributor->id)
            ->orderBy('created_at', 'desc');

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(8);
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

            $category = Category::find($request->category_id);
            $isBatchManaged = $this->isBatchManagedCategory($category->name);

            Log::info('Category check', [
                'category_name' => $category->name,
                'is_batch_managed' => $isBatchManaged
            ]);

            // Step 1: Validate basic info that's always required
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

            // Step 2: If this is a full save, validate sales-related fields
            if (!$request->has('save_product')) {
                if ($isBatchManaged) {
                    $validatedSales = $request->validate([
                        'price' => 'required|numeric|min:0',
                        'minimum_purchase_qty' => 'required|integer|min:1',
                    ]);

                    // Force stock_quantity to 0 for batch-managed products
                    $validatedSales['stock_quantity'] = 0;
                } else {
                    $validatedSales = $request->validate([
                        'price' => 'required|numeric|min:0',
                        'minimum_purchase_qty' => 'required|integer|min:1',
                        'stock_quantity' => 'required|integer|min:0',
                    ]);
                }

                $validatedData = array_merge($validatedData, $validatedSales);
            } else {
                // Partial save: default required sales values
                $validatedData['price'] = 0;
                $validatedData['stock_quantity'] = $isBatchManaged ? 0 : ($request->input('stock_quantity', 0));
                $validatedData['minimum_purchase_qty'] = 1;
            }

            // Step 3: Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('products', $file, $filename);
                $validatedData['image'] = $path;
            }

            // Step 4: Additional data
            $validatedData['distributor_id'] = $distributor->id;
            $validatedData['status'] = 'Accepted';

            // Step 5: Save product
            DB::beginTransaction();
            try {
                $product = Product::create($validatedData);
                Log::info('Product created', ['product_id' => $product->id]);

                // Step 6: Create stock for regular (non-batch) products
                if (
                    !$request->has('save_product') &&
                    !$isBatchManaged &&
                    isset($validatedData['stock_quantity']) &&
                    $validatedData['stock_quantity'] > 0
                ) {
                    \App\Models\Stock::create([
                        'product_id' => $product->id,
                        'batch_id' => null,
                        'type' => 'in',
                        'quantity' => $validatedData['stock_quantity'],
                        'user_id' => $user->id,
                        'notes' => 'Initial stock',
                        'stock_updated_at' => now()
                    ]);
                }

                DB::commit();

                $message = $request->has('save_product')
                    ? 'Product saved successfully (partial save).'
                    : 'Product saved successfully.';

                return redirect()->route('distributors.products.index')
                    ->with('success', $message);
            } catch (\Exception $inner) {
                DB::rollBack();
                Log::error('Transaction failed: ' . $inner->getMessage());
                return back()->with('error', 'Could not save product. Try again.');
            }
        } catch (\Exception $e) {
            Log::error('Outer error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create product: ' . $e->getMessage());
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
                $filename = time() . $file->getClientOriginalName();
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


    public function getProductsList()
    {
        try {
            $user = Auth::user();
            $distributor = $user->distributor;

            if (!$distributor) {
                return response()->json(['error' => 'No distributor profile found.'], 404);
            }

            $products = Product::where('distributor_id', $distributor->id)
                ->select('id', 'product_name', 'image', 'price', 'price_updated_at')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'image_url' => $product->image ? asset('storage/' . $product->image) : asset('img/default-product.jpg'),
                        'price' => $product->price,
                        'price_updated_at' => $product->price_updated_at ? \Carbon\Carbon::parse($product->price_updated_at)->format('M d, Y') : null,
                    ];
                });

            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products list: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch products: ' . $e->getMessage()], 500);
        }
    }

    public function updatePrice(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'price' => 'required|numeric|min:0',
            ]);

            $product = Product::findOrFail($id);

            // Check if this product belongs to the current distributor
            if ($product->distributor_id != Auth::user()->distributor->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Update the price - this line was missing!
            $product->update([
                'price' => $validated['price'],
                'price_updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully',
                'last_updated' => now()->format('M d, Y')
            ]);
        } catch (\Exception $e) {
            Log::error('Price update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update price: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a category requires batch management
     */
    private function isBatchManagedCategory($categoryName)
    {
        $batchCategories = [
            'Ready To Cook',
            'Beverages',
            'Instant Products',
            'Snacks',
            'Sauces & Condiments',
            'Juices & Concentrates',
            'Powdered Products',
            'Frozen Products',
            'Dairy Products'
        ];

        return in_array($categoryName, $batchCategories);
    }
}
