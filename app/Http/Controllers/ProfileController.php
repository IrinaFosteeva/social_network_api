<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use AccountStatus;

class ProfileController extends Controller
{
    public function update(Request $request, $userId)
    {
        $validated = $request->validate([
            'bio' => 'sometimes|string|max:255',
            'gender' => 'sometimes|string|in:male,female',
            'date_of_birth' => 'sometimes|date|before:today',
        ]);
        DB::beginTransaction();
        try {
            $profile = Profile::where('user_id', $userId)->first();
            if ($profile->isEmpty()) {
                return ApiResponseHelper::notFound('Profile found.');
            }
            $profile->update($validated);
            DB::commit();
            return ApiResponseHelper::success(null, 'Profile updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete profile', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return ApiResponseHelper::serverError('An error occurred.');
        }
    }

    public function index(Request $request) {
        try {
            $perPage = (int)$request->input('per_page', 15);
            if ($perPage <= 0) {
                return ApiResponseHelper::badRequest('The per_page parameter must be a positive integer.');
            }
            $profiles = Profile::paginate($perPage);
            if ($profiles->isEmpty()) {
                return ApiResponseHelper::notFound('No profiles found.');
            }
            return ApiResponseHelper::success($profiles, 'Profiles retrieved successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to retrieve profiles', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return ApiResponseHelper::serverError('An error occurred while retrieving profiles.');
        }
    }

    public function show($userId) {
        try {
            $profile = Profile::findOrFail($userId);
            return ApiResponseHelper::success($profile, 'Profile data retrieved successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve profile data', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return ApiResponseHelper::serverError('An error occurred while retrieving profile data.');
        }
    }

    public function destroy($id) {
        $currentUser = Auth::user();

        if ($currentUser->id != $id && !$currentUser->hasRole('admin')) {
            return ApiResponseHelper::forbidden('You do not have access');
        }

        $deletedStatus = AccountStatus::where('status', 'deleted')->first();

        if (!$deletedStatus) {
            return ApiResponseHelper::serverError('Account status "deleted" does not exist.');
        }

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->account_status_id = $deletedStatus->id;
            $user->save();

            DB::commit();

            return ApiResponseHelper::success(null, 'Profile deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete profile', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);
            return ApiResponseHelper::serverError('An error occurred.');
        }
    }
}
