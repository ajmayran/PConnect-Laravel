<?php

namespace App\Http\Controllers\Auth;

use App\Models\Credential;
use App\Models\Distributors;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
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
            'password' => Hash::make($validated['password']),
            'status' => $validated['user_type'] === 'retailer' ? 'approved' : 'pending',
        ]);

        if ($request->hasFile('credentials')) {
            if ($validated['user_type'] === 'distributor') {
                $filePath = $request->file('credentials')->store('credentials/bir', 'public');
        
                // Store file path in the credentials table
                Credential::create([
                    'user_id' => $user->id,
                    'file_path' => $filePath,
                ]);
            } else if ($validated['user_type'] === 'retailer') {
                $filePath = $request->file('credentials')->store('credentials/permit', 'public');
        
                // Store file path in the credentials table
                Credential::create([
                    'user_id' => $user->id,
                    'file_path' => $filePath,
                ]);
            }
        }

        if ($request->hasFile('credentials2')) {
            $filePath2 = $request->file('credentials2')->store('credentials/sec', 'public');
            Credential::create([
                'user_id'   => $user->id,
                'file_path' => $filePath2,
            ]);
        }

        if ($validated['user_type'] === 'distributor') {
            Distributors::create([
                'user_id' => $user->id,
                'company_name' => $request->input('company_name'),
                'company_email' => $request->input('company_email'),
                'company_address' => $request->input('company_address'),
                'company_phone_number' => $request->input('company_phone_number'),
                'bir_form' => $filePath,
                'sec_document' => $filePath2,
            ]);
        }

        event(new Registered($user));

        if ($user->user_type === 'distributor') {
            return redirect()->route('auth.approval-waiting')
                ->with('message', 'Registration successful! Please wait for admin approval.');
        }

        Auth::login($user);
        return redirect(route('retailers.dashboard', absolute: false));
    }

    /**
     * Display the approval waiting view.
     */
    public function approvalWaiting(): View
    {
        return view('auth.approval-waiting');
    }
}
