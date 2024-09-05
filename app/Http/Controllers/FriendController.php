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

        if (!User::find($friendId)) {
            return response()->json([
                'message' => 'Friend '.(int)($friendId).' not found!',
            ], 404);
        }

        if ($user->friends->contains($friendId)) {
            return response()->json([
                'message' => 'Already friends!',
            ], 400);
        }

        $user->friends()->attach($friendId);

        return response()->json([
            'message' => 'Friend added successfully!',
        ]);
    }

    public function remove(Request $request, $userId)
    {
        $friendId = $request->input('friend_id');

        $user = User::findOrFail($userId);
        $user->friends()->detach($friendId);

        return response()->json([
            'message' => 'Friend removed successfully!',
        ]);
    }

    public function index($userId)
    {
        $user = User::findOrFail($userId);
        $friends = $user->friends;

        return response()->json($friends);
    }
}

