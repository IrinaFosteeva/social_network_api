<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\UserLogoutService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller {
    public function register(Request $request) {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'account_status_id' => 1,
            ]);

            $user->profile()->create([
                'nickname' => $validated['name']
            ]);
            $user->assignRole('user');

            DB::commit();
            return ApiResponseHelper::created(['user' => $user], 'User registered successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User registration failed: ' . $e->getMessage());
            return ApiResponseHelper::serverError('Failed to create user or profile. Please try again.');
        }
    }

    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        try {
            $user = User::where('email', $validated['email'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                Log::warning('Unauthorized login attempt', ['email' => $validated['email']]);
                return ApiResponseHelper::unauthorized('Unauthorized, the wrong data');
            }

            if (!$user->isActive()) {
                Log::warning('Inactive user login attempt', ['email' => $validated['email']]);
                return ApiResponseHelper::forbidden('Your account is not active.');
            }

            $user->tokens()->delete();
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            return ApiResponseHelper::success(['token' => $token,
                'user_id' => $user->id,
            ], 'Login successful');

        } catch (\Exception $e) {
            Log::error('Token creation failed', [
                'error' => $e->getMessage(),
                'credentials' => $validated,
            ]);
            return ApiResponseHelper::serverError('Error via token creation');
        }
    }

    public function logout(UserLogoutService $logoutService) {
        try {
            $logoutService->logout();
            return ApiResponseHelper::success([], 'Logged out successfully');
        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
            ]);
            return ApiResponseHelper::serverError('Failed to log out. Please try again.');
        }
    }
}
