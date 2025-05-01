<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributors;
use Illuminate\Support\Facades\Auth;


class AllDistributorController extends Controller
{
    public function index()
    {
        $retailerId = Auth::user()->id; // Get the logged-in retailer's ID
        $distributors = Distributors::with(['followers' => function ($query) use ($retailerId) {
            $query->where('retailer_id', $retailerId);
        }])->get();
    
        // Add is_following property to each distributor
        $distributors->each(function ($distributor) use ($retailerId) {
            $distributor->is_following = $distributor->followers->isNotEmpty();
        });
    
        return view('retailers.all-distributor', [
            'distributors' => $distributors,
        ]);
    }
}
