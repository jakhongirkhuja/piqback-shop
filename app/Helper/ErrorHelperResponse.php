<?php
namespace App\Helper;

class ErrorHelperResponse{

    public static function returnError($message, $responseCode){

        $responseArr['error']=true;
        $responseArr['message'] = $message;
        return response()->json($responseArr, $responseCode);
    }
}