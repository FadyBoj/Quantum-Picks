<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Exceptions\CustomException;
use Exception;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Laravel\Passport\Passport;


class UserController extends Controller
{
    public function notDone(Request $request){
        $routeName = $request->path();
        return ["route" => $routeName];
    }

    //Register

    public function register(Request $request)
    {
        try
        { 
           $data = $request->all();
           User::create($data);
           return response()->json(["msg" => "Your account has been created successfully"],200);
        }
        catch(Exception $e)
        {
            return response()->json(["msg"=>$e->getMessage()],400);
        }
    }

    //Login

    public function login(Request $request){

        try
        {
         
            $credentials =  $request->only("email","password");
            if(!Auth::attempt($credentials))
            throw new CustomException("Email and password are mismatched",400);

            $userId = Auth::user()->id;
            $user = User::find($userId);
            $user->tokens()->delete();
            $token = $user->createToken('My Token')->accessToken;
            $oneDay = 60 * 24;

            return response()->json(["token" => $token],200)
            ->cookie("accessToken",$token,$oneDay,null,null,true,true);

        }
        catch(Exception $e)
        {
            return response()->json(["msg"=>$e->getMessage()],400);
        }
    }

    //Logout

    public function logout(Request $request){

        $user = User::find(Auth::guard('api')->user()->id);
        $user->tokens()->delete();
         return response()->json(["msg"=> "logged out"],200)->withoutCookie('accessToken');;
    }

    //Add to cart

    public function addToCart(Request $request)
    {
        $user = Auth::guard('api')->user();
         return response()->json(["msg" => "Passed $user->firstname"]);

    }

}
