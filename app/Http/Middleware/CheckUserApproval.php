<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->user_type === 'retailer') {
            return $next($request);
        }
        if ($user->user_type === 'distributor') {
            if ($user->status === 'pending') {
                Auth::logout();
                return redirect()->route('auth.approval-waiting')
                    ->with('message', 'Your account is pending approval.');
            }

            if ($user->status === 'rejected') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your account has been rejected. Reason: ' . $user->rejection_reason);
            }

            // If approved, allow access
            if ($user->status === 'approved') {
                return $next($request);
            }
        }

        return redirect()->route('login');
    }
}
