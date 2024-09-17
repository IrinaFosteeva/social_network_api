<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\Following;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowingController extends Controller {
    public function add($followingId) {
        User::findOrFail($followingId);
        $user = Auth::user();

        if ($user->id == $followingId) {
            return ApiResponseHelper::serverError('You cannot follow yourself!');
        }

        if ($user->followings->contains($followingId)) {
            return ApiResponseHelper::serverError('Already followed!');
        }

        DB::beginTransaction();

        try {
            Following::create([
                'user_id' => $user->id,
                'following_id' => $followingId,
            ]);

            DB::commit();
            return ApiResponseHelper::success($followingId, 'Followed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update user data', [
                'error' => $e->getMessage(),
            ]);
            return ApiResponseHelper::serverError('Failed to follow. Please try again.');
        }
    }

    public function destroy($unfollowingId) {
        $user = Auth::user();

        $following = Following::where('user_id', $user->id)
            ->where('following_id', $unfollowingId)
            ->first();

        if (!$following) {
            return ApiResponseHelper::notFound('Not followed!');
        }

        DB::beginTransaction();
        try {
            $following->delete();
            DB::commit();
            return ApiResponseHelper::success($unfollowingId, 'Unfollowed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update user data', [
                'error' => $e->getMessage(),
            ]);
            return ApiResponseHelper::serverError('Failed to unfollow. Please try again.');
        }
    }

    public function followings($userId) {
        $user = User::findOrFail($userId); //поиск сразу в таблице профиля по юзер ид
        if (!$user) {
            return ApiResponseHelper::serverError('User with id ='.$userId.' not found');
        }

        $followings = $user->followings()->with('profile')->get();
        $profiles = $followings->pluck('profile');
        return ApiResponseHelper::success($profiles, 'Ok');
    }

    public function followers($userId) {
        $user = User::findOrFail($userId);
        if (!$user) {
            return ApiResponseHelper::serverError('User with id ='.$userId.' not found');
        }

        $followings = $user->followers()->with('profile')->get();
        $profiles = $followings->pluck('profile');
        return ApiResponseHelper::success($profiles, 'Ok');
    }
}

