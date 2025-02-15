<?php 

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

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
        ]);

        Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => 'pending',
        ]);

        return redirect()->route('retailers.dashboard')->with('success', 'Ticket created successfully.');
    }
}