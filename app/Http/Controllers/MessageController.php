<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MessageController extends Controller {
    public function send(Request $request, $userId) {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $senderUser = Auth::user();
            $receiverUser = User::findOrFail($userId);

            $chatId = Message::where(function($query) use ($senderUser, $receiverUser) {
                $query->where('sender_id', $senderUser->id)
                    ->where('receiver_id', $receiverUser->id);
            })->orWhere(function($query) use ($senderUser, $receiverUser) {
                $query->where('sender_id', $receiverUser->id)
                    ->where('receiver_id', $senderUser->id);
            })->pluck('chat_id')->first();

            if (is_null($chatId)) {
                $chatId = min($senderUser->id, $receiverUser->id) . '-' . max($senderUser->id, $receiverUser->id);
            }

            $message = Message::create([
                'sender_id' => $senderUser->id,
                'receiver_id' => $receiverUser->id,
                'content' => $validated['content'],
                'chat_id' => $chatId,
            ]);

            DB::commit();
            return ApiResponseHelper::created(['message' => $message], 'Message sent successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to send a message', [
                'error' => $e->getMessage(),
            ]);
            return ApiResponseHelper::serverError('An error occurred while sending a message.');
        }
    }


    public function show($chatId) {
        DB::beginTransaction();
        try {
            $senderUser = Auth::user();
            $receiverUser = User::findOrFail($userId);

            $chatId = Message::where(function($query) use ($senderUser, $receiverUser) {
                $query->where('sender_id', $senderUser->id)
                    ->where('receiver_id', $receiverUser->id);
            })->orWhere(function($query) use ($senderUser, $receiverUser) {
                $query->where('sender_id', $receiverUser->id)
                    ->where('receiver_id', $senderUser->id);
            })->pluck('chat_id')->first();

            if (is_null($chatId)) {
                $chatId = min($senderUser->id, $receiverUser->id) . '-' . max($senderUser->id, $receiverUser->id);
            }

            $message = Message::create([
                'sender_id' => $senderUser->id,
                'receiver_id' => $receiverUser->id,
                'content' => $validated['content'],
                'chat_id' => $chatId,
            ]);

            DB::commit();
            return ApiResponseHelper::created(['message' => $message], 'Message sent successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to send a message', [
                'error' => $e->getMessage(),
            ]);
            return ApiResponseHelper::serverError('An error occurred while sending a message.');
        }
    }

    public function index($chatId) {
        $chat = Chat::findOrFail($chatId);
        $messages = $chat->messages()->with('sender')->get();

        return response()->json($messages);
    }

    public function destroy($chatId, $messageId) {
        $chat = Chat::findOrFail($chatId);
        $message = Message::where('chat_id', $chatId)->findOrFail($messageId);

        // Проверяем, что текущий пользователь участник чата и автор сообщения
        if (!$chat->users->contains(Auth::id()) || $message->sender_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();
        return response()->json(['message' => 'Message deleted successfully.'], 200);
    }
}

