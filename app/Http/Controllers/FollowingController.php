<?php

namespace App\Http\Controllers;

use App\Models\Following;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowingController extends Controller
{
    public function add($followingId)
    {
        User::findOrFail($followingId);
        $user = Auth::user();

        if ($user->id == $followingId) {
            return response()->json([
                'message' => 'You cannot follow yourself!',
            ], 400);
        }

        if ($user->followings->contains($followingId)) {
            return response()->json([
                'message' => 'Already followed!',
            ], 400);
        }

        try {
            $newFollow = Following::create([
                'user_id' => $user->id,
                'following_id' => $followingId,
            ]);

            if ($newFollow) {
                return response()->json([
                    'message' => 'Followed successfully!',
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to follow. Please try again.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to follow. Please try again.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function remove($unfollowingId)
    {
        $user = Auth::user();

        $following = Following::where('user_id', $user->id)
            ->where('following_id', $unfollowingId)
            ->first();

        if (!$following) {
            return response()->json([
                'message' => 'Not followed!',
            ], 400);
        }

        try {
            $following->delete();
            return response()->json([
                'message' => 'Unfollowed successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to unfollow. Please try again.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function followings()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $followings = $user->followings;
        return response()->json($followings);
    }

    public function followers()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $followers = $user->followers;

        return response()->json($followers);
    }
}

