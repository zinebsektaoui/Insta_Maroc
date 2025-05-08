<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get all users who have sent or received messages with the authenticated user
        $userId = auth()->id();
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) use ($userId) {
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            });

        // Optionally, mark all messages received by the user as read
        Message::where('receiver_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);

        return view('messages.index', compact('conversations'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'content' => $request->content
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'content' => $message->content,
                    'created_at' => $message->created_at->format('g:i A')
                ]
            ]);
        }

        return back();
    }

    public function show(User $user)
    {
        $authId = auth()->id();
        $messages = Message::where(function($query) use ($authId, $user) {
                $query->where('sender_id', $authId)
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function($query) use ($authId, $user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->where('read', false)
            ->update(['read' => true]);

        return view('messages.show', compact('user', 'messages'));
    }
}
