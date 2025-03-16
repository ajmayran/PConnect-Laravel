<?php 

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RetailerTicketController extends Controller
{
    public function create()
    {
        return view('retailers.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => 'pending',
            'image' => $imagePath,
        ]);

        return redirect()->route('retailers.dashboard')->with('success', 'Ticket created successfully, we will get back to you soon.');
    }
}