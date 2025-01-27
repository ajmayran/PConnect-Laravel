<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CredentialController extends Controller
{
    public function downloadCredential()
    {
        $credential = Auth::user()->credential;

        if ($credential && Storage::exists($credential->file_path)) {
            // Serve the file for download
            return Storage::download($credential->file_path, 'Credential_' . Auth::user()->name);
        }

        return redirect()->back()->with('error', 'File not found or inaccessible.');
    }
}
