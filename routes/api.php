<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleAssignmentController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'checkActive'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users/all', [UserController::class, 'index'])->middleware('role:admin|moderator');
    Route::get('users/{userId}', [UserController::class, 'show'])->middleware(['can:view_users', 'role.owner.check']);
    Route::put('users/{userId}', [UserController::class, 'update'])->middleware(['can:edit_users']);
    Route::delete('users/{userId}', [UserController::class, 'destroy'])->middleware('can:delete_users');
    Route::post('users/{userId}/assign-role', [RoleAssignmentController::class, 'assignRole'])->middleware('can:assign_role');
    Route::put('profile/{userId}', [ProfileController::class, 'update'])->middleware(['can:edit_profiles','role.owner.check']);
    Route::get('profile/all', [ProfileController::class, 'index'])->middleware('can:view_profiles');
    Route::get('profile/{userId}', [ProfileController::class, 'show'])->middleware('can:view_profiles');
    Route::put('profile/{userId}', [ProfileController::class, 'destroy'])->middleware(['can:delete_profiles', 'role.owner.check']);
    Route::post('follow/{userId}', [FollowingController::class, 'add'])->middleware('can:manage_followings');
    Route::delete('follow/{userId}', [FollowingController::class, 'destroy'])->middleware('can:manage_followings');
    Route::get('followings/{userId}', [FollowingController::class, 'followings'])->middleware('can:view_followings');
    Route::get('followers/{userId}', [FollowingController::class, 'followers'])->middleware('can:view_followers');


    Route::post('messages/{userId}', [MessageController::class, 'send'])->middleware('can:send_messages');
    Route::get('messages/{userId}', [MessageController::class, 'show'])->middleware('can:view_messages');
    Route::get('messages', [MessageController::class, 'index'])->middleware('can:view_messages');
    Route::delete('messages/chat/{chat_id}', [MessageController::class, 'destroyChat'])->middleware('can:delete_messages');
    Route::delete('messages/{message_id}', [MessageController::class, 'destroyMessage'])->middleware('can:delete_messages');



    ///TODO


    Route::post('chats', [ChatController::class, 'create']);
    Route::get('chats/{userId}', [ChatController::class, 'show']);
    Route::delete('chats/{chatId}', [ChatController::class, 'destroy']);


    Route::get('messages/{chatId}', [MessageController::class, 'show']);
    Route::post('messages/{chatId}', [MessageController::class, 'index']);
    Route::delete('messages/{messageId}', [ChatController::class, 'destroy']);
    Route::put('messages/{messageId}', [ChatController::class, 'update']);
});

