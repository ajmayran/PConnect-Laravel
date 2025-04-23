<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DiscountsController extends Controller
{
    public function index()
    {
        $distributorId = Auth::user()->distributor->id;

        // Update discount status based on expiration
        $this->updateDiscountStatuses($distributorId);

        // Fetch non-expired discounts with pagination (both active and inactive)
        $discounts = Discount::where('distributor_id', $distributorId)
            ->where(function ($query) {
                $query->whereDate('end_date', '>=', now()->format('Y-m-d'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Fetch expired discounts
        $expiredDiscounts = Discount::where('distributor_id', $distributorId)
            ->where('status', 'expired')
            ->get();

        Log::info('Discount dates:', [
            'discounts' => $discounts->map(function ($d) {
                return [
                    'id' => $d->id,
                    'start_date' => $d->start_date,
                    'end_date' => $d->end_date,
                ];
            })
        ]);

        return view('distributors.discounts.index', compact('discounts', 'expiredDiscounts'));
    }

    /**
     * Update statuses for all discounts belonging to a distributor
     */
    private function updateDiscountStatuses($distributorId)
    {
        $now = Carbon::now();

        // Find all discounts that have ended but aren't marked as expired
        $expiredDiscounts = Discount::where('distributor_id', $distributorId)
            ->where('end_date', '<', $now)
            ->where('status', '!=', 'expired')
            ->get();

        foreach ($expiredDiscounts as $discount) {
            $discount->status = 'expired';
            $discount->save();

            Log::info('Discount marked as expired:', [
                'discount_id' => $discount->id,
                'name' => $discount->name,
                'end_date' => $discount->end_date
            ]);
        }
    }

    public function create()
    {
        $distributorId = Auth::user()->distributor->id;
        $products = Product::where('distributor_id', $distributorId)->get();

        return view('distributors.discounts.create', compact('products'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:percentage,freebie',
            'percentage' => 'required_if:type,percentage|nullable|numeric|min:1|max:100',
            'buy_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
            'free_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);


        // Combine date and time into Carbon instances
        $startDateTime = Carbon::parse("{$validated['start_date']} {$validated['start_time']}")->setTimezone('UTC');
        $endDateTime = Carbon::parse("{$validated['end_date']} {$validated['end_time']}")->setTimezone('UTC');

        // Determine current status
        $status = 'active';
        $now = Carbon::now();
        if ($now->lt($startDateTime)) {
            $status = 'inactive'; // Not started yet
        }


        // Create new discount
        $discount = new Discount([
            'distributor_id' => Auth::user()->distributor->id,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'type' => $validated['type'],
            'percentage' => $validated['type'] == 'percentage' ? $validated['percentage'] : null,
            'buy_quantity' => $validated['type'] == 'freebie' ? $validated['buy_quantity'] : null,
            'free_quantity' => $validated['type'] == 'freebie' ? $validated['free_quantity'] : null,
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'is_active' => $request->has('is_active'),
            'status' => $status
        ]);

        $discount->save();
        // Attach selected products
        if (!empty($validated['product_ids'])) {
            $discount->products()->attach($validated['product_ids']);
        }


        return redirect()->route('distributors.discounts.index')->with('success', 'Discount created successfully!');
    }

    public function edit(Discount $discount)
    {
        $this->authorize('update', $discount);

        $distributorId = Auth::user()->distributor->id;
        $products = Product::where('distributor_id', $distributorId)->get();
        $selectedProductIds = $discount->products->pluck('id')->toArray();

        return view('distributors.discounts.edit', compact('discount', 'products', 'selectedProductIds'));
    }

    public function update(Request $request, $id)
    {
        try {
            $discount = Discount::findOrFail($id);

            // Authorize the action
            $this->authorize('update', $discount);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'type' => 'required|in:percentage,freebie',
                'percentage' => 'required_if:type,percentage|nullable|numeric|min:1|max:100',
                'buy_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
                'free_quantity' => 'required_if:type,freebie|nullable|integer|min:1',
                'start_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_date' => 'required|date|after_or_equal:start_date',
                'end_time' => 'required|date_format:H:i',
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id',
            ]);

            // Combine date and time into Carbon instances
            $startDateTime = Carbon::parse("{$validated['start_date']} {$validated['start_time']}");
            $endDateTime = Carbon::parse("{$validated['end_date']} {$validated['end_time']}");

            // Determine status based on dates and active flag
            $status = $this->getDiscountStatus($startDateTime, $endDateTime);

            // Update discount with validated data
            $discount->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'type' => $validated['type'],
                'percentage' => $validated['type'] == 'percentage' ? $validated['percentage'] : null,
                'buy_quantity' => $validated['type'] == 'freebie' ? $validated['buy_quantity'] : null,
                'free_quantity' => $validated['type'] == 'freebie' ? $validated['free_quantity'] : null,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'is_active' => $request->has('is_active'),
                'status' => $status
            ]);

            // Sync products
            $discount->products()->sync($validated['product_ids']);

            return response()->json([
                'success' => true,
                'message' => 'Discount updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update discount: ' . $e->getMessage()
            ], 500);
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

        try {
            $discount->is_active = !$discount->is_active;
            // If toggling to active, check if it should be marked as expired
            if ($discount->is_active && $discount->isExpired()) {
                $discount->status = 'expired';
            } else if ($discount->is_active) {
                $discount->status = 'active';
            } else {
                $discount->status = 'inactive';
            }

            $discount->save();

            return redirect()->route('distributors.discounts.index')
                ->with('success', 'Discount status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error toggling discount status:', ['error' => $e->getMessage()]);
            return redirect()->route('distributors.discounts.index')
                ->with('error', 'Failed to update discount status.');
        }
    }

    public function show($id)
    {
        $discount = Discount::findOrFail($id);
        return view('distributors.discounts.show', compact('discount'));
    }

    public function expired()
    {
        $distributorId = Auth::user()->distributor->id;

        // Fetch only expired discounts
        $expiredDiscounts = Discount::where('distributor_id', $distributorId)
            ->where(function ($query) {
                $query->where('end_date', '<', now())
                    ->orWhere('status', 'expired');
            })
            ->orderBy('end_date', 'desc')
            ->get();

        if (request()->ajax()) {
            return view('distributors.discounts.expired', compact('expiredDiscounts'))->render();
        }

        // Fallback for non-AJAX requests
        return view('distributors.discounts.expired', compact('expiredDiscounts'));
    }

    /**
     * Determine the discount status based on dates and active flag
     * 
     * @param \Carbon\Carbon $startDateTime
     * @param \Carbon\Carbon $endDateTime
     * @return string
     */
    private function getDiscountStatus($startDateTime, $endDateTime)
    {
        $now = now();

        if ($now->gt($endDateTime)) {
            return 'expired';
        } elseif ($now->gte($startDateTime)) {
            return 'active';
        } else {
            return 'scheduled';
        }
    }

    /**
     * Create a command to check and update discount statuses
     */
    public function checkExpiredDiscounts()
    {
        $now = Carbon::now();

        // Find all discounts that have ended but aren't marked as expired
        $expiredDiscounts = Discount::where('end_date', '<', $now)
            ->where('status', '!=', 'expired')
            ->get();

        $count = $expiredDiscounts->count();

        foreach ($expiredDiscounts as $discount) {
            $discount->status = 'expired';
            $discount->save();

            Log::info('Discount marked as expired:', [
                'discount_id' => $discount->id,
                'name' => $discount->name,
                'end_date' => $discount->end_date
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} discounts marked as expired"
        ]);
    }
}
