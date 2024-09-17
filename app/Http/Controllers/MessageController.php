<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MessageController extends Controller {
    public function show(Request $request, $chatId) {

    }


    public function index($chatId) {
    }

    public function update($messageId) {

    }

    public function destroy($messageId) {

    }
}

