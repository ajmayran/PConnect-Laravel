<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use App\Models\DistributorFollower;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributorFollowController extends Controller
{
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
        $distributor = Distributors::find($distributorId);
        
        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'follower_count' => $distributor->followers_count,
            'message' => $message
        ]);
    }

    public function checkFollowStatus(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id'
        ]);

        $isFollowing = DistributorFollower::where('distributor_id', $request->distributor_id)
            ->where('retailer_id', Auth::id())
            ->exists();

        return response()->json([
            'is_following' => $isFollowing
        ]);
    }
}