<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatService {
    public function createChat(array $validated): ?array {
        if ($validated['type'] === 'private') {
            return $this->createPrivateChat($validated);
        }

        if ($validated['type'] === 'public') {
            return $this->createPublicChat($validated);
        }
        return null;
    }

    private function createPrivateChat(array $validated): array {
        if (count($validated['users_id']) > 2) {
            return ['error' => 'Private chat cannot include more than 2 users.'];
        }

        $existingChat = $this->checkExistingPrivateChat($validated['users_id']);
        if ($existingChat) {
            return ['success' => $existingChat, 'message' => 'Private chat already exists.'];
        }

        return $this->saveChat($validated);
    }

    private function createPublicChat(array $validated): array {
        if (count($validated['users_id']) < 3) {
            return ['error' => 'Public chat cannot include less than 3 users.'];
        }

        // TODO: Implement public chat creation logic
        return ['success' => '', 'message' => 'Not working yet, need TODO'];
    }

    private function saveChat(array $validated): array {
        DB::beginTransaction();
        try {
            $chat = Chat::create([
                'type' => $validated['type'],
                'name' => $validated['name'] ?? null,
            ]);

            $chat->users()->attach($validated['users_id']);

            DB::commit();
            return ['success' => ['chat_id' => $chat->id], 'message' => 'Chat created successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DB error during saving chat', [
                'error' => $e->getMessage(),
            ]);
            return ['success' => 'false', 'message' => 'Failed to create chat'];
        }
    }

    private function checkExistingPrivateChat($users): ?array {

        $usersCount = count($users);

        try {
            if ($usersCount == 1) {
                $subQuery = Chat::select('chat_user.chat_id')
                    ->join('chat_user', 'chats.id', '=', 'chat_user.chat_id')
                    ->where('chats.type', 'private')
                    ->groupBy('chat_user.chat_id')
                    ->havingRaw('COUNT(chat_user.user_id) = 1');

                $existingChat = Chat::select('chat_user.chat_id')
                    ->join('chat_user', 'chats.id', '=', 'chat_user.chat_id')
                    ->where('chat_user.user_id', $users[0])
                    ->whereIn('chat_user.chat_id', $subQuery)
                    ->first();
            } else {
                $existingChat = Chat::select('chats.id as chat_id')
                    ->join('chat_user', 'chats.id', '=', 'chat_user.chat_id')
                    ->where('chats.type', 'private')
                    ->whereIn('chat_user.user_id', $users)
                    ->groupBy('chats.id')
                    ->havingRaw('COUNT(DISTINCT chat_user.user_id) = ?', [$usersCount])
                    ->first();
            }

            if ($existingChat) {
                return $existingChat;
            }

        } catch (\Exception $e) {
            Log::error('DB error during checkExistingPrivateChat', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function validateChatData($request) {
        return $request->validate([
            'type' => 'required|string|in:private,public',
            'name' => 'sometimes|string|max:150',
            'users_id' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (!in_array(Auth::id(), $value)) {
                        $fail('Your ID must be in the users_id array, you must add it.');
                    }
                },
                'max:100',
            ],
            'users_id.*' => 'integer|distinct|exists:users,id',
        ]);
    }
}

