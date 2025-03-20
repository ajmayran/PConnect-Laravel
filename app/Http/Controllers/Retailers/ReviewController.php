<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use App\Models\Distributors;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    /**
     * Store a new review.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        // Check if the user has already reviewed this distributor
        $existingReview = Review::where('user_id', Auth::id())
            ->where('distributor_id', $validated['distributor_id'])
            ->first();

        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating' => $validated['rating'],
                'review' => $validated['review'],
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->back()->with('success', 'Your review has been updated!');
        }

        // Create new review
        Review::create([
            'user_id' => Auth::id(),
            'distributor_id' => $validated['distributor_id'],
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return redirect()->back()->with('success', 'Thank you for your review!');
    }
}