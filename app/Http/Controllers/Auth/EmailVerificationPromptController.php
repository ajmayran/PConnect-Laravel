<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            if ($request->user()->user_type === 'distributor') {
                return redirect()->route('auth.approval-waiting')
                    ->with('message', 'Email verified! Please wait for admin approval.');
            }
            
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Use the standard auth.verify-email view instead of an email template
        return view('auth.verify-email');
    }
}