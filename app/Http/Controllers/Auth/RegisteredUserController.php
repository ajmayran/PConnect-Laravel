<?php

namespace App\Http\Controllers\Auth;

use App\Models\Credential;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\DistributorRegistrationMail;
use App\Mail\RetailerRegistrationMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function createRetailer(): View
    {
        return view('auth.register-retailer');
    }

    public function createDistributor(): View
    {
        return view('auth.register-distributor');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'middle_name' => ['nullable', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique('users')->where(function ($query) {
                $query->where('status', 'approved'); // Only check for approved users
            }),
        ],
        'user_type' => ['required', 'in:retailer,distributor'],
        'credentials' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:20480'],
        'credentials2' => ['required_if:user_type,distributor', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:20480'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'middle_name' => $validated['middle_name'],
        'user_type' => $validated['user_type'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
    ]);

    // Save the first credential (BIR Form)
    if ($request->hasFile('credentials')) {
        $filePath = $request->file('credentials')->store('credentials', 'public');

        Credential::create([
            'user_id' => $user->id,
            'file_path' => $filePath,
            'type' => 'bir_form', // Set type as 'bir_form'
        ]);
    }

    // Save the second credential (SEC Document) if applicable
    if ($request->hasFile('credentials2')) {
        $filePath2 = $request->file('credentials2')->store('credentials', 'public');

        Credential::create([
            'user_id' => $user->id,
            'file_path' => $filePath2,
            'type' => 'sec_document', // Set type as 'sec_document'
        ]);
    }

    return redirect()->route('login')->with('success', 'Registration successful. Please wait for approval.');
}

    /**
     * Display the approval waiting view.
     */
    public function approvalWaiting(): View
    {
        return view('auth.approval-waiting');
    }
}
