<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request)
    {
        // Get user from request parameters instead of relying on session
        $user = User::find($request->route('id'));

        if (!$user) {
            return redirect('/login')->with('error', 'User not found.');
        }

        // Check if the email hash matches
        if (!hash_equals(sha1($user->email), $request->route('hash'))) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            // Log in the user if not already authenticated
            if (!Auth::check()) {
                Auth::login($user);
            }

            return $this->redirectBasedOnUserType($user)
                ->with('info', 'Email already verified. Welcome back!');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Log in the user after verification
        Auth::login($user);

        // Regenerate session to ensure security
        Session::regenerate();

        return $this->redirectBasedOnUserType($user)
            ->with('verified', true)
            ->with('success', 'Email verified successfully!');
    }

    /**
     * Redirect based on user type.
     */
    protected function redirectBasedOnUserType(User $user): RedirectResponse
    {
        // If distributor, check approval status
        if ($user->user_type === 'distributor') {
            if ($user->status === 'approved') {
                return $user->profile_completed 
                    ? redirect()->route('distributors.dashboard')
                    : redirect()->route('distributors.setup');
            }
            return redirect()->route('auth.approval-waiting');
        }

        // For retailers
        return redirect()->route('retailers.dashboard');
    }
}