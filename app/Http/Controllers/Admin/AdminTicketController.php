<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class AdminTicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'user.retailerProfile', 'user.distributor'])->where('status', 'pending')->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['user', 'user.retailerProfile', 'user.distributor'])->findOrFail($id);
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

    public function resolved()
    {
        $tickets = Ticket::with(['user', 'user.retailerProfile', 'user.distributor'])->where('status', 'resolved')->get();
        return view('admin.tickets.resolved', compact('tickets'));
    }

    public function rejected()
    {
        $tickets = Ticket::with(['user', 'user.retailerProfile', 'user.distributor'])->where('status', 'rejected')->get();
        return view('admin.tickets.rejected', compact('tickets'));
    }
}