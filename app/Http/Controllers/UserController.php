<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Serviсes\UserLogoutService;

class UserController extends Controller {
    public function index(Request $request) {
        try {
            $perPage = (int)$request->input('per_page', 15);
            if ($perPage <= 0) {
                return response()->json([
                    'message' => 'The per_page parameter must be a positive integer.',
                ], 400);
            }
            $users = User::select('id', 'name', 'email', 'created_at')
                ->paginate($perPage);

            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'No users found.',
                ], 404);
            }

            return response()->json($users);

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve users', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'An error occurred while retrieving users.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id) {
        try {
            if ($id != Auth::id()) {
                return response()->json([
                    'error' => 'You do not have access to this information.',
                ], 403);
            }

            $user = User::with('profile')->findOrFail($id);
            return response()->json($user);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found.',
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve user data', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'An error occurred while retrieving user data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id) {
        try {
            if ($id != Auth::id()) {
                return response()->json([
                    'error' => 'You do not have access to this information.',
                ], 403);
            }

            $user = User::findOrFail($id);
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:6',
            ]);

            $user->update($validated);
            return response()->json($user, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found.',
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Failed to update user', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'An error occurred while updating user information.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id, UserLogoutService $logoutService) {
        if ($id != Auth::id()) {
            return response()->json([
                'error' => 'You do not have access to this information.',
            ], 403);
        }

        try {
            $user = User::findOrFail($id);
            $user->delete();
            $logoutService->logout();
            return response()->json('', 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Failed to delete user', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'An error occurred while trying to delete the user.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}

