<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EnsureProfileIsCompleted
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // List of routes exempted from profile and subscription checks
        $exemptRoutes = [
            'distributors.setup',
            'profile.updateSetup',
            'distributors.subscription',
            'distributors.subscription.paymongo',
            'distributors.subscription.success',
            'distributors.subscription.cancel',
            'logout'
        ];

        // Always allow access to exempt routes
        if (in_array($request->route()->getName(), $exemptRoutes)) {
            return $next($request);
        }

        // Only enforce checks for distributors
        if ($user->user_type === 'distributor') {
            // Redirect to profile setup if not completed
            if (!$user->profile_completed) {
                return redirect()->route('distributors.setup')
                    ->with('warning', 'Please complete your profile setup first.');
            }

            // Redirect to subscription page if not seen
            if (!$user->has_seen_subscription_page) {
                // Update the `has_seen_subscription_page` field to true
                $user->has_seen_subscription_page = true;
                \App\Models\User::where('id', $user->id)->update(['has_seen_subscription_page' => true]);

                return redirect()->route('distributors.subscription')
                    ->with('info', 'Please choose a subscription plan to continue.');
            }
        }

        return $next($request);
    }
}
