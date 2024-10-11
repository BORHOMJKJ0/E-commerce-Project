<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ResponseHelper;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('email', $request->email)->first();
        if (!empty($user) && $user->email_verified_at === null) {
            return ResponseHelper::jsonResponse([], 'your email address is not verified', 403, false);
        }

        return $next($request);
    }
}
