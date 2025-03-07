<?php

namespace App\Http\Controllers\Distributors;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributorMessageController extends Controller
{
    public function index()
    {
        // Get retailers for the sidebar
        $retailers = User::where('user_type', 'retailer')
            ->whereHas('retailerProfile')
            ->with('retailerProfile')  
            ->get();
            
        // Get current conversation if any retailer is selected
        $currentRetailerId = request()->query('retailer');
        $messages = collect();
        $currentRetailer = null;
        
        if ($currentRetailerId) {
            $currentRetailer = User::with('retailerProfile')->find($currentRetailerId);
            if ($currentRetailer) {
                $messages = $this->getConversation($currentRetailerId);
                
                // Mark messages as read
                Message::where('sender_id', $currentRetailerId)
                    ->where('receiver_id', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
            }
        }
        
        return view('distributors.messages.index', compact('retailers', 'messages', 'currentRetailer'));
    }

    public function getConversation($retailerId)
    {
        return Message::where(function($query) use ($retailerId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $retailerId);
            })
            ->orWhere(function($query) use ($retailerId) {
                $query->where('sender_id', $retailerId)
                      ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);
        
        // Broadcast the message
        event(new MessageSent($message->message, Auth::id(), $request->receiver_id));
        
        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }
    
    public function getUnreadCount()
    {
        
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['unread_count' => $unreadCount]);
    }
    
    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
        ]);
        
        Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
            
        return response()->json(['status' => 'success']);
    }
}
