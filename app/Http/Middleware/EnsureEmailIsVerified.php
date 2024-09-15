<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            $user = User::where('email', $request->eamil)->first();
        } else {
            $user = $request->user();
        }
        if ($user->email_verified_at === null) {
            return response()->json(['message' => 'your email address is not verified'], 403);
        }

        return $next($request);
    }
}
