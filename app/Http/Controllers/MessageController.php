<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller {
    public function send(Request $request) {
        $request->headers->set('Accept', 'application/json');
        $currentUserId = Auth::id();

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if (!(User::find($validated['receiver_id']))) {
            return response()->json([
                'message' => 'Receiver not found.',
            ], 404);
        }
        $validated['sender_id'] = $currentUserId;

        try {
            $message = Message::create($validated);
            return response()->json($message, 201);
        } catch (\Exception $e) {
            \Log::error('Failed to create message', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'An error occurred while creating the message.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index() {
        $currentUserId = Auth::id();
        $messages = Message::where(function ($query) use ($currentUserId) {
            $query->where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId);
        })->get();

        if ($messages->isEmpty()) {
            return response()->json([
                'message' => 'No messages found.',
            ], 404);
        }

        return response()->json($messages);
    }

    public function conversation($userId) {
        $currentUserId = Auth::id();

        if (!User::find($userId)) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $messages = Message::where(function ($query) use ($userId, $currentUserId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $currentUserId);
        })->orWhere(function ($query) use ($userId, $currentUserId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $userId);
        })->get();

        if ($messages->isEmpty()) {
            return response()->json([
                'message' => 'No messages found between these users.',
            ], 404);
        }

        return response()->json($messages);
    }
}

