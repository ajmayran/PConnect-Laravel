<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // First, check if the email is already verified
        if ($request->user()->hasVerifiedEmail()) {
            if ($request->user()->user_type === 'distributor') {
                return redirect()->route('auth.approval-waiting');
            }
            
            // For retailers with already verified email, redirect to retailer dashboard
            return redirect()->route('retailers.dashboard');
        }

        // Mark email as verified and fire the event
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Handle user type specific redirections
        if ($request->user()->user_type === 'distributor') {
            return redirect()->route('auth.approval-waiting')
                ->with('message', 'Email verified! Please wait for admin approval.');
        }
        
        // For retailers, redirect directly to retailer dashboard instead of HOME
        return redirect()->route('retailers.dashboard')
            ->with('verified', true);
    }
}