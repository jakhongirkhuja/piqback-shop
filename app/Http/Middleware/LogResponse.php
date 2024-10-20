<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class LogResponse
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
        $response = $next($request);

        
            $log = [
                'ENDPOINT'=>'Server',
                'URI' => $request->getUri(),
                'METHOD' => $request->getMethod(),
                'REQUEST_BODY' => $request->all(),
                'RESPONSE' => json_decode($response->getContent()),
                'exception' => $response->exception,
            ];
            $apiToken = "5742929322:AAEIYasWSlEjDK38bB_MaKpTNFRixyAGh5g";
     
             $data = [
                 'chat_id' => '-1001892747504',
                 'text' => json_encode($log)
             ];
             try {
                file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
             } catch (\Throwable $th) {
                Log::info(json_encode($log));
             }
            
            Log::info(json_encode($log));
        return $response;
    }
}
