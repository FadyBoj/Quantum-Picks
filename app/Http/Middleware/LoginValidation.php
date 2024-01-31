<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;


class LoginValidation
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
            $request->validateWithBag('login',[
                "email" => "required|email:rfc,dns|exists:users",
                "password" => "required"
            ],[

            ]);

            if(!User::where("email",$request->email)->first()->regular)
            throw new CustomException("This email is signed using google",400);

        }
        
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
        return $next($request);
    }
}
