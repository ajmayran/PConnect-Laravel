<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Ticket;

class AdminTicketController extends Controller
{
    public function __construct() {
        Log::info('AdminTicketController: Constructed');
    }

    public function index()
    {
        Log::info('AdminTicketController: Index Accessed'); // Logging!
        $tickets = Ticket::with('user.distributor', 'user.retailer')->where('status', 'pending')->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::with('user.distributor', 'user.retailer')->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function resolve(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => 'resolved']);
        return redirect()->route('admin.tickets.index')->with('success', 'Ticket resolved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        return redirect()->route('admin.tickets.index')->with('success', 'Ticket rejected successfully.');
    }

    public function rejected()
    {
        Log::info('AdminTicketController: Rejected Accessed'); // Logging!
        $tickets = Ticket::with('user.distributor', 'user.retailer')->where('status', 'rejected')->get();
        return view('admin.tickets.rejected', compact('tickets'));
    }
}