<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DistributorSubscription;
use Carbon\Carbon;

class CheckSubscription
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
        $user = Auth::user();
        
        // Skip this middleware if not a distributor
        if ($user->user_type !== 'distributor') {
            return $next($request);
        }
        
        // If this is a subscription-related route, allow access
        if ($this->isSubscriptionRoute($request)) {
            return $next($request);
        }
        
        // First check if profile is completed
        if (!$user->profile_completed) {
            return $next($request); // Let EnsureProfileIsCompleted handle this
        }
        
        $distributor = $user->distributor;
        
        if (!$distributor) {
            return redirect()->route('distributors.setup');
        }
        
        // Check registration date - give 1 month free trial
        $registeredDate = $user->created_at;
        $trialExpiry = $registeredDate->copy()->addMonth();
        
        // If still within trial period, allow access
        if (Carbon::now()->lt($trialExpiry)) {
            return $next($request);
        }
        
        // Check for active subscription
        $hasActiveSubscription = DistributorSubscription::where('distributor_id', $distributor->id)
            ->where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->exists();
        
        if ($hasActiveSubscription) {
            return $next($request);
        }
        
        // No active subscription, redirect to subscription page
        return redirect()->route('distributors.subscription')
            ->with('warning', 'Your trial period has expired. Please subscribe to continue using all features.');
    }
    
    private function isSubscriptionRoute(Request $request)
    {
        $subscriptionRoutes = [
            'distributors.subscription',
            'distributors.subscription.paymongo',
            'distributors.subscription.success',
            'distributors.subscription.cancel',
            'logout'
        ];
        
        return in_array($request->route()->getName(), $subscriptionRoutes);
    }
}