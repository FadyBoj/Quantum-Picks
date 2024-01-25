<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\CustomException;
use App\Models\User;

class UserAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userID = Auth::guard('api')->user()->id;
        $user = User::find($userID);
        $userAddresses = $user->addresses()->get();

        if($user->admin == 1)
        throw new CustomException("Not authorized",401);

        if(!$userAddresses || count($userAddresses) == 0)
        throw new CustomException("Address must be provided",404);

        return $next($request);
    }
}
