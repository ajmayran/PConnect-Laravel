<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $senderId = Auth::id();
        $receiverId = $request->input('receiver_id');
        $messageText = $request->input('message');

        // Save message to database
        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $messageText,
        ]);

        // Broadcast the message
        broadcast(new MessageSent($messageText, $senderId, $receiverId))->toOthers();

        return response()->json(['message' => $message]);
    }

    public function getMessages($receiverId)
    {
        $userId = Auth::id();

        // Get messages between the authenticated user and the receiver
        $messages = Message::where(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $userId)->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', $userId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
}
