<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
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
        if (auth()->user() != null) {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                $exp = JWTAuth::getPayload()->get('exp');
                $now = time();

                // Check if the token is about to expire (within 10 minutes)
                if ($exp - $now < 600) {
                    $token = JWTAuth::refresh(JWTAuth::getToken());
                    $user = JWTAuth::setToken($token)->toUser();
                    $request->headers->set('Authorization', 'Bearer ' . $token);
                }
            } catch (TokenExpiredException $e) {
                // Token has expired, do nothing
            } catch (\Exception $e) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $next($request);
        } else {
            return $next($request);
        }

    }
}
