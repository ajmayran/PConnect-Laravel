<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userType = $request->user()->user_type;

        // If user type doesn't match required role
        if ($userType !== $role) {
            return match ($userType) {
                'admin' => redirect()->route('admin.dashboard.index'),
                'retailer' => redirect()->route('retailers.index'),
                'distributor' => redirect()->route('distributors.index'),
                default => redirect()->route('login')
            };
        }

        return $next($request);
    }
}
