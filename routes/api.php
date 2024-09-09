<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleAssignmentController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('users/{userId}/assign-role', [RoleAssignmentController::class, 'assignRole']);
    Route::put('profile/{userId}', [ProfileController::class, 'update']);
    Route::post('follow/{userId}', [FollowingController::class, 'add']);
    Route::delete('follow/{userId}', [FollowingController::class, 'destroy']);
    Route::get('followings/{userId}', [FollowingController::class, 'followings']);
    Route::get('followers/{userId}', [FollowingController::class, 'followers']);
    Route::post('messages/{userId}', [MessageController::class, 'send']);
    Route::get('messages/{userId}', [MessageController::class, 'conversation']);
    Route::get('messages/{userId}/all', [MessageController::class, 'index']);
});

