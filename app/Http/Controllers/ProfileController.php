<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show($userId)
    {
        return Profile::where('user_id', $userId)->firstOrFail();
    }

    public function update(Request $request, $userId)
    {
        $profile = Profile::where('user_id', $userId)->firstOrFail();

        $validated = $request->validate([
            'bio' => 'sometimes|string|max:255',
        ]);

        $profile->update($validated);

        return response()->json($profile);
    }
}
