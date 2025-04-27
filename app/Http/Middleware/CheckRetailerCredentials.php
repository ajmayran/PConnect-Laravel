<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRetailerCredentials
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        if ($user->user_type === 'retailer' && $user->status === 'rejected') {
            return redirect()->route('retailers.credentials.reupload')
                ->with('warning', 'Your credentials have been rejected. Please upload valid business permits to continue using the platform.');
        }

        return $next($request);
    }
}