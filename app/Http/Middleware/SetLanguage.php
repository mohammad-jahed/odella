<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('lang');

        if ($lang == 'ar') {

            app()->setLocale('ar');
        }

        return $next($request);
    }
}
