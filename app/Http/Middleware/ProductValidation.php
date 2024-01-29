<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

use function Laravel\Prompts\error;

class ProductValidation
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
            $request->validateWithBag('product',[
                "title" => "required|unique:products|min:3",
                "description" => "required",
                "price" => "required|numeric",
                "quantity" => "required|numeric",
                "category" => "required"
              ],
              [
            ]);

            if(!$request->hasFile("images"))
            throw new CustomException("Images must be provided",400);

            if(!is_array($request->file("images")) || count($request->file("images")) > 3)
            throw new CustomException("You can't upload more than 3 images per product",400);


            $validExtentions  = [
                "jpg",
                'jpeg',
                'png',
                'webp',
                'bmp',
                'svg',
            ];

            foreach($request->file('images') as $image)
            {
                $ext = $image->getClientOriginalExtension();
                if(!in_array($ext,$validExtentions))
                throw new CustomException('Only images are accepted',400);
            }
        }
        catch (\Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
       
        return $next($request);
    }
}
