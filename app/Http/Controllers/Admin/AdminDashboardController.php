<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Distributors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Fetch the number of active retailers
        $activeRetailersCount = User::where('user_type', 'retailer')->where('status', 'active')->count();
    
        // Fetch the number of visitors (if the visitors table exists)
        $visitorsCount = DB::table('visitors')->count();
    
        // Fetch the number of active orders
        $activeOrdersCount = DB::table('orders')->where('status', 'active')->count();
    
        // Fetch the number of completed orders
        $completedOrdersCount = DB::table('orders')->where('status', 'completed')->count();
    
        // Fetch the number of canceled orders
        $canceledOrdersCount = DB::table('orders')->where('status', 'canceled')->count();
    
        // Fetch the total number of users
        $totalUsersCount = User::count();
    
        // Pass the data to the view
        return view('admin.dashboard', [
            'user' => Auth::user(),
            'activeRetailersCount' => $activeRetailersCount,
            'visitorsCount' => $visitorsCount,
            'activeOrdersCount' => $activeOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'canceledOrdersCount' => $canceledOrdersCount,
            'totalUsersCount' => $totalUsersCount,
        ]);
    }

}
