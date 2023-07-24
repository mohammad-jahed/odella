<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;


class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle($request, Closure $next)
    {
        $token = JWTAuth::getToken();

        try {
            if ($token && JWTAuth::getPayload($token)->get('exp') - time() < config('jwt.refresh_ttl') * 60) {
                $token = JWTAuth::refresh($token);
                JWTAuth::setToken($token)->toUser();
            }
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'message' => 'Token has been blacklisted.',
            ], 401);
        }

        return $next($request);
    }
}
