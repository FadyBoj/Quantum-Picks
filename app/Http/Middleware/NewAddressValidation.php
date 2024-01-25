<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\CustomException;


class NewAddressValidation
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

            $request->validateWithBag('newAddress',[
                "street" => "required|string|min:3",
                "city" => "required|string|min:2",
                "country" => "required|string:min:2"
            ],
            [
                
            ]);

            return $next($request);

        }
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
    }
}
