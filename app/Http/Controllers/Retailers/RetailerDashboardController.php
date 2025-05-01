<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Product;
use App\Models\Distributors;
use App\Models\DistributorFollower;
use Illuminate\Http\Request;
use App\Models\BlockedRetailer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RetailerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $retailerId = $user->id;

        // Get IDs of distributors who have blocked this retailer
        $blockingDistributorIds = BlockedRetailer::where('retailer_id', $retailerId)
            ->pluck('distributor_id')
            ->toArray();

        // Get all distributors, including those who blocked the retailer
        $allDistributors = Distributors::with(['followers' => function ($query) use ($retailerId) {
            $query->where('retailer_id', $retailerId);
        }])
        ->take(3)
        ->get();

        // Add "is_blocked" and "is_following" flags to each distributor
        $distributors = $allDistributors->map(function ($distributor) use ($blockingDistributorIds, $retailerId) {
            $distributor->is_blocked = in_array($distributor->user_id, $blockingDistributorIds);
            $distributor->is_following = $distributor->followers->isNotEmpty();
            return $distributor;
        });

        // Show all products EXCEPT those from distributors who have blocked this retailer
        $products = Product::whereDoesntHave('distributor', function ($query) use ($blockingDistributorIds) {
            $query->whereIn('user_id', $blockingDistributorIds);
        })
            ->where('price', '>', 0)  // Only show products with a price greater than 0
            ->with(['distributor:id,company_name,user_id', 'discounts' => function ($query) {
                $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            }])
            ->with('distributor:id,company_name,user_id')  // Optimize queries with eager loading
            ->paginate(15);

        return view('retailers.dashboard', [
            'user' => $user,
            'distributors' => $distributors,
            'products' => $products,
            'hasBlockedDistributors' => count($blockingDistributorIds) > 0
        ]);
    }
}