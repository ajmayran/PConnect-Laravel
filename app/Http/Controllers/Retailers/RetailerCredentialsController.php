<?php

namespace App\Http\Controllers\Retailers;

use App\Models\User;
use App\Models\Credential;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RetailerCredentialsController extends Controller
{
    public function showReuploadForm()
    {
        $user = \App\Models\User::find(Auth::id());
        return view('retailers.credentials-reupload', [
            'rejection_reason' => $user->rejection_reason,
            'status' => $user->status
        ]);
    }

    public function processReupload(Request $request)
    {
        // Get a fresh instance of the user from database
        $user = User::find(Auth::id());

        // Check if user is already in pending status
        if ($user->status === 'pending') {
            return redirect()->route('retailers.credentials.reupload')
                ->with('warning', 'Your credentials are already submitted and awaiting approval. Please wait for admin review.');
        }

        $request->validate([
            'credentials' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Delete old credentials if they exist
        foreach ($user->credentials as $credential) {
            Storage::disk('public')->delete($credential->file_path);
            $credential->delete();
        }

        // Store new credentials
        if ($request->hasFile('credentials')) {
            $filePath = $request->file('credentials')->store('credentials', 'public');

            Credential::create([
                'user_id' => $user->id,
                'file_path' => $filePath,
            ]);

            // Set credentials status to pending for admin review
            $user->update([
                'status' => 'pending',
                'rejection_reason' => null // Clear previous rejection reason
            ]);
        }

        return redirect()->route('retailers.credentials.reupload')
            ->with('success', 'Your credentials have been submitted and are pending verification. You will be notified once they are approved.');
    }
}
