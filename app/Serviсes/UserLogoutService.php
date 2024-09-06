<?php

namespace App\Serviсes;

use Illuminate\Support\Facades\Auth;

class UserLogoutService {
    public function logout()
    {
        $user = Auth::user();

        try {
            $user->tokens()->delete();
        } catch (\Exception $e) {
            \Log::error('Failed to log out', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
