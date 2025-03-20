<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BlockedRetailer;
use Illuminate\Support\Facades\Auth;

class CheckDistributorBlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $distributorId = $request->route('id');
        $retailerId = Auth::id();

        $isBlocked = BlockedRetailer::where('distributor_id', $distributorId)
            ->where('retailer_id', $retailerId)
            ->exists();

        if ($isBlocked) {
            return redirect()->route('retailers.dashboard')
                ->with('error', 'You cannot access this distributor\'s page because you have been blocked.');
        }

        return $next($request);
    }
}