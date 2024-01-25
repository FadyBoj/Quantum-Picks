<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Exceptions\CustomException;
use Exception;
use App\Models\User;
use App\Models\Cart;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Laravel\Passport\Passport;
use PhpParser\Node\Stmt\Return_;


use function PHPUnit\Framework\isNull;
use function PHPUnit\Framework\returnSelf;

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

            //move cart items from cookies to database
            $cartItems = json_decode($request->cookie('cart'));
            if($cartItems)
            {
                $formatted_cart_items = [];

                foreach($cartItems as $item)
                {
                    $formatted_cart_items[] = [
                        "id" => $item->id,
                        "user_id" => $userId,
                        "quantity" => $item->quantity
                    ];
                }

                //Removing user cart items from the database if exist
                Cart::where('user_id',$user->id)->delete();
                Cart::insert($formatted_cart_items);

            }

            return response()->json(["token" => $token],200)
            ->cookie("accessToken",$token,$oneDay,null,null,true,true)->withoutCookie('cart');

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
        try
        {   
            //Getting request data
            $data = $request->only('id','quantity') ;

            //Getting user if authenticated
            $isAuthenticated = $request->attributes->get('isAuthenticated');
            $userId = $isAuthenticated ? (Auth::guard('api')->user())->id : null;
            $user = $isAuthenticated ? User::find($userId) : null; 
            
        
            //Getting cart items
            $cartItems = json_decode($request->cookie('cart')) ?: ($isAuthenticated ? $user->cart_items()->get() : []);

          
            foreach($cartItems as $item )
            {
                $item->id == $data['id'] ? throw new CustomException('Product already in the cart',400) : "";
            }
          

            $newCartItem = [
                    "id" => $data["id"],
                    "user_id" => $isAuthenticated ? $user->id : null,
                    "quantity" => $data["quantity"],
            ];

            $cartItems[] = $newCartItem;

            //Storing cart in cookie if user not authenticated
            if(!$isAuthenticated)
            return response()->json(["msg" => "Successfully added product to cart"],200)
            ->cookie(
                "cart",
                json_encode($cartItems),
                60 * 24 * 5, //Five days to expire
                null,
                null,
                true, // Secure
                true // Http Only
            );

            //Storing cart in database if user is authenticated
            Cart::create($newCartItem);
            return response()->json(["msg" => "Successfully added product to cart"],200);


        }

        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
        
    }

    //Getting cart items

    public function getCartItems(Request $request)
    {
        //Getting user if authenticated
        $isAuthenticated = $request->attributes->get('isAuthenticated');
        $userId = $isAuthenticated ? (Auth::guard('api')->user())->id : null;
        $user = $isAuthenticated ? User::find($userId) : null; 

        //Getting cart items
        $cartItems = json_decode($request->cookie('cart')) ?: ($isAuthenticated ? $user->cart_items()->get() : null);

        if(!$cartItems || count($cartItems) == 0)
        throw new CustomException('Cart is empty',400);

        return $cartItems;
    }

    //Remove item from cart
    public function removeFromCart(Request $request)
    {
        
        try
        {
            //Validate incoming request

            $request->validateWithBag('removeFromCart',[
                "id" => "required|numeric"
            ],[]);

            $productID = $request->id;

            //Getting user if authenticated
            $isAuthenticated = $request->attributes->get('isAuthenticated');
            $userId = $isAuthenticated ? (Auth::guard('api')->user())->id : null;
            $user = $isAuthenticated ? User::find($userId) : null; 

            //Getting cart items
            $cartItems = !$isAuthenticated ?  json_decode($request->cookie('cart')) : null;

           if(!$isAuthenticated)
           {
            $cartItems = json_decode($request->cookie('cart'));

            if(!$cartItems || count($cartItems) == 0)
            throw new CustomException('Cart is already empty.',400);

            if(count($cartItems) == 1)
            return response()->json(["msg" => "Successfully removed product from your cart."],200)
            ->withoutCookie('cart');

            //Filtering cart items
            $filtered_cart_items = [];
            
            foreach($cartItems as $item)
            {
                if($item->id !== $productID)
                $filtered_cart_items[] = $item;
                
            }


            return response()->json(['msg'=> 'Successfully removed product from your cart.'],200)
            ->cookie(
                "cart",
                json_encode($filtered_cart_items),
                60 * 24 * 5, //Five days to expire
                null,
                null,
                true, // Secure
                true // Http Only
            );

           }
           else
           {
            Cart::where('user_id',$user->id)
            ->where('id',$productID)
            ->delete();

            return response()->json(['msg'=> 'Successfully removed product from your cart.'],200);

           }
        }
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }

    }

    //Clear cart

    public function clearCart(Request $request)
    {
        //Getting user if authenticated
        $isAuthenticated = $request->attributes->get('isAuthenticated');
        $userId = $isAuthenticated ? (Auth::guard('api')->user())->id : null;

        if(!$isAuthenticated)
        {
            $cartItems = json_decode($request->cookie('cart'));
            return (!$cartItems || count($cartItems) == 0) ? 
            response()->json(["msg" => "Cart is already empty."],400)
            :
            response()->json(["msg" => "Cart cleared."],200)
            ->withoutCookie('cart');
        }

        $user = User::find($userId);
        Cart::where('user_id',$user->id)->delete();

        return response()->json(["msg" => "Cart cleared."],200);
    }

}
