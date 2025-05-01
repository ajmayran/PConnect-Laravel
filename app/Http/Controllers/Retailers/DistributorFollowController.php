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


    public function following()
    {
        $followedDistributors = DistributorFollower::where('retailer_id', Auth::id())
            ->with('distributor')
            ->get();

        return view('retailers.profile.following', compact('followedDistributors'));
    }

    public function index()
    {
        $distributorId = Auth::user()->distributor->id;

        // Fetch followers with pagination (20 per page)
        $followers = DistributorFollower::where('distributor_id', $distributorId)
            ->with('retailer') // Eager load retailer details
            ->paginate(20);

        return view('distributors.followers.index', compact('followers'));
    }

    public function remove($id)
    {
        $follower = DistributorFollower::findOrFail($id);

        // Ensure the follower belongs to the logged-in distributor
        if ($follower->distributor_id !== Auth::user()->distributor->id) {
            abort(403, 'Unauthorized action.');
        }

        $follower->delete();

        return redirect()->route('distributors.followers.index')->with('success', 'Follower removed successfully.');
    }
}
