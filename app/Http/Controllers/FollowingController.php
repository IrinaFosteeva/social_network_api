<?php

namespace App\Http\Controllers;

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
            $user->followings()->attach($followingId);
            $user->load('followings');
            if ($user->followings->contains($followingId)) {
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
        User::findOrFail($unfollowingId);
        $user = Auth::user();

        if (!$user->followings->contains($unfollowingId)) {
            return response()->json([
                'message' => 'Not followed!',
            ], 400);
        }

        try {
            $user->followings()->detach($unfollowingId);
            $user->load('followings');
            if (!$user->followings->contains($unfollowingId)) {
                return response()->json([
                    'message' => 'Unfollowed successfully!',
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to unfollow. Please try again.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to unfollow. Please try again.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
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

