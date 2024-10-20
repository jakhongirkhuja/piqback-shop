<?php

namespace App\Http\Middleware;

use App\Helper\ErrorHelperResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckBotToken
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
        $token ='dG9rZW5sa2Rqc2FsMTIza2xhamFzbGtkamFsa2QyMSFBc0Bhc2Q=';
        if (request()->token!=$token) {
            return ErrorHelperResponse::returnError('token is not correct',Response::HTTP_BAD_REQUEST);
        }
        return $next($request);
    }
}
