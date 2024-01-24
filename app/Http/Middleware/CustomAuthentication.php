<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;

class CustomAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie("accessToken") ?: $request->bearerToken();
        $request->headers->set("Authorization","Bearer " . $token);

        if(!$token)
        throw new CustomException("Token must be provided",401);

        if(!Auth::guard('api')->check())
        throw new CustomException("unauthenticated",401);


        return $next($request);
    }
}
