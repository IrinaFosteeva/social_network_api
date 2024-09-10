<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Serviсes\UserLogoutService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {
    public function index(Request $request) {
        try {
            $perPage = (int)$request->input('per_page', 15);
            if ($perPage <= 0) {
                return ApiResponseHelper::badRequest('The per_page parameter must be a positive integer.');
            }
            $users = User::with('profile')->paginate($perPage);
            if ($users->isEmpty()) {
                return ApiResponseHelper::notFound('No users found.');
            }
            return ApiResponseHelper::success($users, 'Users retrieved successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to retrieve users', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return ApiResponseHelper::serverError('An error occurred while retrieving users.');
        }
    }

    public function show($id) {
        $currentUser = Auth::user();
        if ($currentUser->hasRole('user') && $id != $currentUser->id) {
            return ApiResponseHelper::forbidden('You do not have access to this information.');
        }
        try {
            $user = User::with('profile')->findOrFail($id);
            return ApiResponseHelper::success($user, 'User data retrieved successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve user data', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);
            return ApiResponseHelper::serverError('An error occurred while retrieving user data.');
        }
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => [
                'sometimes',
                'string',
                'min:8',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/'
            ],
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            $userData = [
                'name' => $validated['name'] ?? $user->name,
                'email' => $validated['email'] ?? $user->email,
            ];

            if (isset($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);
            DB::commit();

            return ApiResponseHelper::success($user, 'User data updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update user data', [
                'error' => $e->getMessage(),
                'user_id' => $id,
            ]);
            return ApiResponseHelper::serverError('An error occurred while updating user data.');
        }
    }

    public function destroy($id) {
        $currentUser = Auth::user();
        if ($currentUser->id == $id) {
            return ApiResponseHelper::forbidden('Admin cannot delete their own account.');
        }

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();

            return ApiResponseHelper::success(null, 'User deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);
            return ApiResponseHelper::serverError('An error occurred while trying to delete the user.');
        }
    }
}

