<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function add(Request $request, $userId)
    {
        $friendId = $request->input('friend_id');

        $user = User::findOrFail($userId);

        if (!User::findOrFail($friendId)) {
            return response()->json([
                'message' => 'User '.$friendId.' not found!',
            ], 404);
        }

        if ($user->friends->contains($friendId)) {
            return response()->json([
                'message' => 'Already friends!',
            ], 400);
        }

        try {
            $user->friends()->attach($friendId);
            if ($user->friends->contains($friendId)) {
                return response()->json([
                    'message' => 'Friend added successfully!',
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to add friend. Please try again.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add friend. Please try again.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function remove(Request $request, $userId)
    {
        $friendId = $request->input('friend_id');

        $user = User::findOrFail($userId);

        if (!User::find($friendId)) {
            return response()->json([
                'message' => 'User '.$friendId.' not found!',
            ], 404);
        }

        if (!$user->friends->contains($friendId)) {
            return response()->json([
                'message' => 'Not friends!',
            ], 400);
        }

        try {
            $user->friends()->detach($friendId);
            if (!$user->friends->contains($friendId)) {
                return response()->json([
                    'message' => 'Friend removed successfully!',
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to remove friend. Please try again.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove friend. Please try again.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    public function index($userId)
    {
        $user = User::findOrFail($userId);
        $friends = $user->friends;

        return response()->json([$friends]);
    }
}

