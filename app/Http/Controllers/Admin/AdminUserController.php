<?php 

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Get the user_type filter from the request
        $userType = $request->get('user_type');

        // Fetch users, filtering only for 'retailer' and 'distributor' user types
        $users = User::whereIn('user_type', ['retailer', 'distributor'])
            ->when($userType, function ($query, $userType) {
                return $query->where('user_type', $userType);
            })
            ->paginate(10);

        // Define navigation links for user types
        $navigationLinks = [
            'retailer' => route('admin.allRetailers'),
            'distributor' => route('admin.allDistributors'),
        ];

        return view('admin.users.index', compact('users', 'userType', 'navigationLinks'));
    }
}