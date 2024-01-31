<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;


class RegisterValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{

            // converting email to lower case 

            $request->merge([
                "email" => strtolower($request->email), 
                "verification_code" => mt_rand(1000000,9999999),
                "vCode_date" => Date::now(),
                "regular" => true,
                "google" => false,
                "complete" => true
            ]);

            //Validate the user data before storing it it Database

            $request->validateWithBag('register',[
                'email' => 'email:rfc,dns|required|unique:users',
                'firstname' => 'required|string||min:3|alpha',
                'lastname' => 'required|string|alpha|min:3|alpha',
                'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
                'password_confirmation' => 'required'
            ],[
                'email.unique' => "This email is already taken",
            ]);

            //Hashing the passsword

            $request->merge([
                "password" => Hash::make($request->password)    
            ]);

        }
        catch(\Exception $e){
            throw new CustomException($e->getMessage(),400);

        }

        return $next($request);
    }
}
