<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApprovedDistributor
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

        if ($user->user_type !== 'distributor') {
            Log::info('Access denied for user type: ' . $user->user_type);
            return redirect()->route('login');
        }
        if ($user->status !== 'approved') {
            Auth::logout();
            return redirect()->route('auth.approval-waiting');
        }

        return $next($request);
    }
}
