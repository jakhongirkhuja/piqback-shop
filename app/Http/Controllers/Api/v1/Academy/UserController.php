<?php

namespace App\Http\Controllers\Api\v1\Academy;

use App\Http\Controllers\Controller;
use App\Models\Money\Iqc;
use App\Models\Phonebook;
use App\Models\Store\KorzinkaBarcodeOrder;
use App\Models\StoreLatest\StoreProductCode;
use App\Models\StoreLatest\StoreSellerReport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function userInfo()
    {
        $response['user']= auth()->user();
        $response['iqc']=Iqc::where('user_id',auth()->user()->id)->first();
        $response['qrcode']=StoreProductCode::with('product')->where('user_id',auth()->user()->id)->latest()->get();
        $response['korzinkaqrcode']=KorzinkaBarcodeOrder::where('toUser',auth()->user()->id)->latest()->get();
        return response()->json($response, Response::HTTP_OK);
    }
    
    
    public function phoneNumberSearch()
    {
        $phoneNumber =request()->s;
       
        if($phoneNumber){
            $phoneNumberList = Phonebook::where('phoneNumber','like','%'.$phoneNumber.'%')->take(10)->get();
            return response()->json($phoneNumberList, Response::HTTP_OK);
        } 
    }
}
