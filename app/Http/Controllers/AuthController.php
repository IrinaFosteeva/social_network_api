<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Serviсes\UserLogoutService;


class AuthController extends Controller {
    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->profile()->create();
            return response()->json(['user' => $user], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create user or profile. Please try again.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized, the wrong data'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        if (!$token) {
            return response()->json([
                'error' => 'Error via token creation',
            ], 500);
        }

        return response()->json(['token' => $token]);
    }


    public function logout(UserLogoutService $logoutService)
    {
        try {
            $logoutService->logout();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to log out. Please try again.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
