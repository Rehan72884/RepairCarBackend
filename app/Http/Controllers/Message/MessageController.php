<?php

namespace App\Http\Controllers\Message;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $sender = Auth::user();
        $receiver = \App\Models\User::findOrFail($request->receiver_id);

        // Allow only Admin <-> Client OR Admin <-> Expert communication
        $isAdminSender = $sender->hasRole('Admin');
        $isClientSender = $sender->hasRole('Client');
        $isExpertSender = $sender->hasRole('Expert');

        $isAdminReceiver = $receiver->hasRole('Admin');
        $isClientReceiver = $receiver->hasRole('Client');
        $isExpertReceiver = $receiver->hasRole('Expert');

        $validCombination =
            // Admin <-> Client
            ($isAdminSender && $isClientReceiver) ||
            ($isClientSender && $isAdminReceiver) ||

            // Admin <-> Expert
            ($isAdminSender && $isExpertReceiver) ||
            ($isExpertSender && $isAdminReceiver);

        if (! $validCombination) {
            return response()->json(['error' => 'Invalid recipient'], 403);
        }

        $message = \App\Models\Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }


    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        if ($message->receiver_id === Auth::id()) {
            $message->is_read = true;
            $message->save();
        }

        return response()->json(['status' => 'Message marked as read']);
    }
}
