<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow access to setup routes if profile is not completed
        if (!$user->profile_completed) {
            if ($request->routeIs('distributors.setup') || $request->routeIs('profile.updateSetup')) {
                return $next($request);
            }
            return redirect()->route('distributors.setup')
                ->with('warning', 'Please complete your profile setup first.');
        }

        // Prevent access to setup if profile is already completed
        if ($request->routeIs('distributors.setup')) {
            return redirect()->route('distributors.dashboard')
                ->with('info', 'Your profile is already completed.');
        }

        return $next($request);
    }
}
