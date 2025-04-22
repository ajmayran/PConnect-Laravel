<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DiscountsController extends Controller
{
    public function index()
    {

        Log::info('Current Timezone: ' . config('app.timezone'));
        $distributorId = Auth::user()->distributor->id;
        $discounts = Discount::where('distributor_id', $distributorId)
            ->orderBy('is_active', 'desc')
            ->orderBy('end_date', 'desc')
            ->paginate(10);
            
        return view('distributors.discounts.index', compact('discounts'));
    }
    
    public function create()
    {
        $distributorId = Auth::user()->distributor->id;
        $products = Product::where('distributor_id', $distributorId)->get();
        
        return view('distributors.discounts.create', compact('products'));
    }
    
    public function store(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:percentage,freebie',
            'percentage' => 'required_if:type,percentage|nullable|numeric|min:1|max:100',
            'buy_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
            'free_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create discount
            $discount = Discount::create([
                'distributor_id' => $distributorId,
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'type' => $validated['type'],
                'percentage' => $validated['type'] == 'percentage' ? $validated['percentage'] : null,
                'buy_quantity' => $validated['type'] == 'freebie' ? $validated['buy_quantity'] : null,
                'free_quantity' => $validated['type'] == 'freebie' ? $validated['free_quantity'] : null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $request->has('is_active'),
            ]);
            
            // Attach products
            $discount->products()->attach($validated['product_ids']);
            
            DB::commit();
            
            return redirect()->route('distributors.discounts.index')
                ->with('success', 'Discount created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create discount: ' . $e->getMessage());
        }
    }
    
    public function edit(Discount $discount)
    {
        $this->authorize('update', $discount);
        
        $distributorId = Auth::user()->distributor->id;
        $products = Product::where('distributor_id', $distributorId)->get();
        $selectedProductIds = $discount->products->pluck('id')->toArray();
        
        return view('distributors.discounts.edit', compact('discount', 'products', 'selectedProductIds'));
    }
    
    public function update(Request $request, Discount $discount)
    {
        $this->authorize('update', $discount);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:percentage,freebie',
            'percentage' => 'required_if:type,percentage|nullable|numeric|min:1|max:100',
            'buy_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
            'free_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            $discount->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'type' => $validated['type'],
                'percentage' => $validated['type'] == 'percentage' ? $validated['percentage'] : null,
                'buy_quantity' => $validated['type'] == 'freebie' ? $validated['buy_quantity'] : null,
                'free_quantity' => $validated['type'] == 'freebie' ? $validated['free_quantity'] : null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $request->has('is_active'),
            ]);
            
            // Sync products
            $discount->products()->sync($validated['product_ids']);
            
            DB::commit();
            
            return redirect()->route('distributors.discounts.index')
                ->with('success', 'Discount updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update discount: ' . $e->getMessage());
        }
    }
    
    public function destroy(Discount $discount)
    {
        $this->authorize('delete', $discount);
        
        try {
            $discount->delete();
            return redirect()->route('distributors.discounts.index')
                ->with('success', 'Discount deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete discount: ' . $e->getMessage());
        }
    }
    
    public function toggle(Discount $discount)
    {
        $this->authorize('update', $discount);
        
        $discount->update([
            'is_active' => !$discount->is_active
        ]);
        
        return redirect()->route('distributors.discounts.index')
            ->with('success', 'Discount status updated successfully!');
    }
}