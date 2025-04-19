<?php

namespace App\Http\Controllers\Retailers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Distributors;
use App\Models\DistributorFollower;
use Illuminate\Http\Request;
use App\Models\BlockedRetailer;
use App\Models\DistributorReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class DistributorPageController extends Controller
{
    public function show($id, Request $request)
    {
        $retailerId = Auth::id();
        $distributor = Distributors::findOrFail($id);

        // Check if this distributor has blocked the retailer
        $isBlocked = BlockedRetailer::where('distributor_id', $distributor->user_id)
            ->where('retailer_id', $retailerId)
            ->exists();

        // Get categories for the distributor
        $categories = Category::whereHas('products', function ($query) use ($id) {
            $query->where('distributor_id', $id);
        })->get();

        // Selected category
        $selectedCategory = $request->category ?? 'all';

        // Only get products if not blocked
        if (!$isBlocked) {
            $productsQuery = Product::where('distributor_id', $id)
                ->where('price', '>', 0);

            if ($selectedCategory !== 'all') {
                $productsQuery->where('category_id', $selectedCategory);
            }

            // Apply pagination - 10 products per page
            $products = $productsQuery->paginate(10);
        } else {
            // Create an empty paginator if blocked
            $products = Product::where('id', 0)->paginate(10);
        }

        // Get average rating for the distributor
        $rating = Review::where('distributor_id', $id)->avg('rating');

        // Check if the user is following this distributor
        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = DistributorFollower::where('distributor_id', $id)
                ->where('retailer_id', $retailerId)
                ->exists();
        }

        // Get follower count from the relationship
        $followerCount = DistributorFollower::where('distributor_id', $id)->count();
        $distributor->followers_count = $followerCount;

        return view('retailers.distributor-page', [
            'distributor' => $distributor,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'isBlocked' => $isBlocked,
            'rating' => $rating,
            'isFollowing' => $isFollowing
        ]);
    }

    public function reportDistributor(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'details' => 'nullable|string',
        ]);

        $user = Auth::user();
        $distributor = Distributors::findOrFail($id);

        // Create report record
        DistributorReport::create([
            'retailer_id' => $user->id,
            'distributor_id' => $id,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Report submitted successfully. Our team will review it shortly.');
    }

    /**
     * Toggle follow/unfollow status for a distributor
     */
    public function toggleFollow(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id'
        ]);

        $distributorId = $request->distributor_id;
        $retailerId = Auth::id();
        
        $existingFollow = DistributorFollower::where('distributor_id', $distributorId)
            ->where('retailer_id', $retailerId)
            ->first();
        
        if ($existingFollow) {
            // Already following, so unfollow
            $existingFollow->delete();
            $isFollowing = false;
            $message = 'You have unfollowed this distributor';
        } else {
            // Not following, so follow
            DistributorFollower::create([
                'distributor_id' => $distributorId, 
                'retailer_id' => $retailerId
            ]);
            $isFollowing = true;
            $message = 'You are now following this distributor';
        }
        
        // Get updated follower count
        $followerCount = DistributorFollower::where('distributor_id', $distributorId)->count();
        
        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'follower_count' => $followerCount,
            'message' => $message
        ]);
    }
}