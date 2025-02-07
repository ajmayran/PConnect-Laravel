<?php

namespace App\Http\Controllers\Auth;

use App\Models\Credential;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'user_type' => ['required', 'in:retailer,distributor'],
            'credentials' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:20480'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $credentialsPath = $request->file('credentials')->store('credentials', 'public');

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'],
            'user_type' => $validated['user_type'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['user_type'] === 'retailer' ? 'approved' : 'pending'
        ]);

        if ($request->hasFile('credentials')) {
            $filePath = $request->file('credentials')->store('credentials', 'public');

            // Store file path in the credentials table
            Credential::create([
                'user_id' => $user->id,
                'file_path' => $filePath,
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
}
