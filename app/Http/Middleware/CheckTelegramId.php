<?php

namespace App\Http\Middleware;

use App\Helper\ErrorHelperResponse;
use App\Models\StoreLatest\SellerTelegram;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckTelegramId
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
       
        
        if (!request()->telegram_id) {
            return ErrorHelperResponse::returnError('Telegram id not exist as parametr',Response::HTTP_BAD_REQUEST);
        }
        $checkSellerTelegram = SellerTelegram::where('telegram_id',request()->telegram_id )->first();
        if(!$checkSellerTelegram){
            return ErrorHelperResponse::returnError('Telegram id is not assigned to User, please register another number',Response::HTTP_BAD_REQUEST);
        }
        return $next($request);
    }
}
