<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountStatus;

class CheckUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->account_status_id !== AccountStatus::where('status', 'active')->value('id')) {
            return ApiResponseHelper::forbidden('Your account is not active.');
        }
        return $next($request);
    }
}

