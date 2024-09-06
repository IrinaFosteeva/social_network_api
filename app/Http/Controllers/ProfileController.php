<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $userId = Auth::id();
        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile) {
            return response()->json([
                'message' => 'Profile not found.',
            ], 404);
        }

        $validated = $request->validate([
            'bio' => 'sometimes|string|max:255',
        ]);

        if ($profile->update($validated)) {
            return response()->json([
                'message' => 'Profile updated successfully!',
                'profile' => $profile
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update profile.',
            ], 500);
        }
    }
}
