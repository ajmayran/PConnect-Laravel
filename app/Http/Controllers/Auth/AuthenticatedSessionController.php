<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Handle user type specific redirects
        return match ($user->user_type) {
            'admin' => redirect()->route('admin.dashboard'),
            'retailer' => redirect()->route('retailers.dashboard'),
            'distributor' => match ($user->status) {
                'approved' => redirect()->route('distributors.dashboard'),
                'pending' => $this->handlePendingDistributor($request),
                default => redirect()->route('login')
            },
            default => redirect()->route('login')
        };
    }

    /**
     * Handle pending distributor logout and redirect
     */
    private function handlePendingDistributor(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.approval-waiting');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
