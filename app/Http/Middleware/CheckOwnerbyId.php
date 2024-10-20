<?php

namespace App\Http\Middleware;

use App\Helper\ErrorHelperResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckOwnerbyId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        if (!request()->hrid) {
            return ErrorHelperResponse::returnError('user id not given as parametr',Response::HTTP_BAD_REQUEST);
        }
        $user = User::where('role','Store Owner')->where('hrid',request()->hrid)->first();
        if(!$user){
            return ErrorHelperResponse::returnError('User not found or  not store owner',Response::HTTP_BAD_REQUEST);
        }
        return $next($request);
    }
}
