<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'store']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::post('users/{userId}/followings', [FollowingController::class, 'add']);
    Route::delete('users/{userId}/followings', [FollowingController::class, 'remove']);
    Route::get('followings', [FollowingController::class, 'index']);
    Route::get('followers', [FollowingController::class, 'followers']);
    Route::post('messages', [MessageController::class, 'send']);
    Route::get('messages/{userId1}/{userId2}', [MessageController::class, 'index']);
});

