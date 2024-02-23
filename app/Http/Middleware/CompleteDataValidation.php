<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompleteDataValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try
        {
            $user = Auth::guard('api')->user();

            if($user->complete)
            throw new CustomException("Not found",404);

            $request->validateWithBag('complete',[
                "firstname" => "required|min:3|alpha",
                "lastname" => "required|min:3|alpha"
            ],[

            ]);
        }
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }

        return $next($request);
    }
}
