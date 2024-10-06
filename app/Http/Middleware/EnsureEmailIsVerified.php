<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['successful' => false,'error' => 'Email is Invalid'], Response::HTTP_NOT_FOUND);
        }
        if ($user->email_verified_at === null) {
            return response()->json(['successful' => false, 'message' => 'your email address is not verified'], 403);
        }

        return $next($request);
    }
}
