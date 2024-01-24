<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CartFormValidation
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
            $request->validateWithBag('cart',[
                "id" => "exists:products|required",
                "quantity" => "numeric|required"
            ],[ 
                "id.exists" => "Product not found.",
                "id.required" => "The Product id field is required.",
                "quantity.numeric" => "quantity must be a numeric value."
            ]);

            return $next($request);
        }
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
        
    }
}
