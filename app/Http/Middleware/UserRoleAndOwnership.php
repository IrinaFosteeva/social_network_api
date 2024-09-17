<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponseHelper;

class UserRoleAndOwnership
{
    public function handle($request, Closure $next)
    {
        $currentUser = Auth::user();
        $userId = $request->route('userId');
        if ($currentUser->hasRole('user') && $userId != $currentUser->id) {
            return ApiResponseHelper::forbidden('1You do not have access to this information.');
        }
        return $next($request);
    }
}

