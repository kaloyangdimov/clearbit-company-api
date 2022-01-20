<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return response()->json('Unauthorized', 401);
        }

        $user = User::firstWhere('token', $request->bearerToken());

        if (!$user || ($user && (now() > $user->token_valid_to))) {
            return response()->json('Unauthorized', 401);
        }

        Auth::login($user);

        return $next($request);
    }
}
