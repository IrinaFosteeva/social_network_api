<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use App\Services\ChatService;

class ChatController extends Controller {
    public function create(Request $request, ChatService $chatService) {
        $validated = $chatService->validateChatData($request);
        $result = $chatService->createChat($validated);

        if (isset($result['error'])) {
            return ApiResponseHelper::error($result['error']);
        }

        return ApiResponseHelper::success($result['success'], $result['message']);
    }

    public function show($userId, Request $request) {

    }

    public function destroy($chatId, Request $request) {

    }
}
