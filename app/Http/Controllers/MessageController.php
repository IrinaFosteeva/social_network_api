<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
        'sender_id' => 'required|exists:users,id',
        'receiver_id' => 'required|exists:users,id',
        'content' => 'required|string',
    ]);

        if ($validated['sender_id'] === $validated['receiver_id']) {
            return response()->json([
                'message' => 'Sender and receiver cannot be the same person.',
            ], 400);
        }

        $sender = User::find($validated['sender_id']);
        $receiver = User::find($validated['receiver_id']);

        if (!$sender || !$receiver) {
            return response()->json([
                'message' => 'Sender or receiver not found.',
            ], 404);
        }

        try {
            $message = Message::create($validated);

            if (!$message) {
                return response()->json([
                    'message' => 'Failed to create message.',
                ], 500);
            }

            return response()->json($message, 201);
        } catch (\Exception $e) {
            \Log::error('Failed to create message', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'An error occurred while creating the message.',
            ], 500);
        }
    }

    public function index($userId1, $userId2)
    {
        $messages = Message::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)
                ->where('receiver_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)
                ->where('receiver_id', $userId1);
        })->get();

        return response()->json($messages);
    }
}

