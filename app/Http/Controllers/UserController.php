<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Exceptions\CustomException;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

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

            return response()->json(["token" => $token],200);

            return ["msg" => "You've passed"];
        }
        catch(Exception $e)
        {
            return response()->json(["msg"=>$e->getMessage()],400);
        }
    }

    //Logout

    public function logout(Request $request){
        Auth::forgetUser();
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $user->tokens()->delete();


        return response()->json(["msg"=> "logged out"],200);
    }

}
