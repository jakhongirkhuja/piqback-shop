<?php

namespace App\Http\Middleware;

use App\Helper\ErrorHelperResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CheckStandardAttributes
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
        $validator = Validator::make($request->all(), [
            'platform' =>'required|string|max:50',
            'device'   =>'required|string|max:50',
            'browser'  =>'required|string|max:50',
            'timeZone' =>'required|string|max:50',
        ]);
        
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        return $next($request);
    }
}
