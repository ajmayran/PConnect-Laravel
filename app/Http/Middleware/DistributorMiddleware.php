<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->user_type !== 'distributor') {
            return redirect('/login');
        }
        $user = Auth::user();

        if ($user->profile_completed === 0) {
            return redirect()->route('distributors.setup'); // Redirect to profile setup
        } elseif ($user->profile_completed === 1) {
            return redirect()->route('distributors.dashboard'); // Redirect to distributor dashboard
        }

        return $next($request);
    }
}
