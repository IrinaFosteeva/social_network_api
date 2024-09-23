<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    public function createChat(array $validated)
    {
        if ($validated['type'] === 'private') {
            return $this->createPrivateChat($validated);
        }

        if ($validated['type'] === 'public') {
            return $this->createPublicChat($validated);
        }

        return null; // or throw an exception
    }

    private function createPrivateChat(array $validated)
    {
        if (count($validated['users_id']) > 2) {
            return ['error' => 'Private chat cannot include more than 2 users.'];
        }

        $existingChat = $this->checkExistingPrivateChat($validated['users_id']);
        if ($existingChat) {
            return ['success' => $existingChat, 'message' => 'Private chat already exists.'];
        }

        return $this->saveChat($validated);
    }

    private function createPublicChat(array $validated)
    {
        if (count($validated['users_id']) < 3) {
            return ['error' => 'Public chat cannot include less than 3 users.'];
        }

        // TODO: Implement public chat creation logic
        return ['success' => 'Not working yet, need TODO'];
    }

    private function saveChat(array $validated)
    {
        DB::beginTransaction();
        try {
            $chat = Chat::create([
                'type' => $validated['type'],
                'name' => $validated['name'] ?? null,
            ]);

            $chat->users()->attach($validated['users_id']);

            DB::commit();
            return ['success' => $chat, 'message' => 'Chat created successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Failed to create chat: ' . $e->getMessage()];
        }
    }

    private function checkExistingPrivateChat($users) {
        sort($users);
        $existingChat = Chat::where('type', 'private')
            ->whereHas('users', function ($query) use ($users) {
                $query->whereIn('users.id', $users)
                    ->groupBy('chat_user.chat_id')
                    ->havingRaw('COUNT(DISTINCT users.id) = ?', [count($users)]);
            })
            ->first();

//        return response()->json([
//            'tst',
//        ]);

        if ($existingChat) {
            $userIds = $existingChat->users->pluck('id')->sort()->values()->toArray();
            if ($userIds === $users) {
                return $existingChat;
            }
        }

        return null; // No chat found
    }

    public function validateChatData($request)
    {
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

