<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function downloadCredential()
    {
        // Get the authenticated user's credential
        $credential = Auth::user()->credential;

        // Check if the user has a credential
        if ($credential) {
            // Get the full path of the file
            $filePath = storage_path('app/public/' . $credential->file_path);

            // Check if the file exists
            if (file_exists($filePath)) {
                // Get the file extension from the original file
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

                // Create a custom file name for download (e.g., Credential_JohnDoe.pdf)
                $fileName = 'Credential_' . Auth::user()->name . '.' . $fileExtension;

                // Return the file for download with the custom name
                return response()->download($filePath, $fileName);
            }
        }

        // If no file or file doesn't exist
        return redirect()->back()->with('error', 'File not found or inaccessible.');
    }
}
