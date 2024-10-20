<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\StoreLatest\SellerTelegram;
use App\Models\StoreLatest\StoreSeller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreSellerTGController extends Controller
{
    public function storeSellerTGList()
    {
        $seller_id = request()->seller_id;
       
        if($seller_id){
            $storeTelegramList = SellerTelegram::where('seller_id',$seller_id)->first();
            if($storeTelegramList){
                return response()->json($storeTelegramList, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Telegram id not exist on this seller',Response::HTTP_NOT_FOUND);
            }
        }
        return ErrorHelperResponse::returnError('Seller with given id not found',Response::HTTP_NOT_FOUND);
    }
    public function storeSellerTGSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telegram_id'=>'required|numeric',
            'seller_id'=>'required|numeric',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        $storeSellerCheck = StoreSeller::find($data['seller_id']);
        if(!$storeSellerCheck){
            return ErrorHelperResponse::returnError('Store seller with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                
                $sellerTelegram = new SellerTelegram();
                $sellerTelegram->saveModel($data, 0);
                $responseArr['SellerTelegram'] =$sellerTelegram;
                $responseArr['message'] = 'Seller Telegram has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
   
    public function storeSellerTGDelete(Request $request, $storeSellerTG_id)
    {
        $storeSeller = SellerTelegram::find($storeSellerTG_id);
        if(!$storeSeller){
            return ErrorHelperResponse::returnError('Seller id  with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($storeSeller){
            try {
                $res = DB::transaction(function () use ($storeSeller, $data){
                    $storeSeller->deleteModel($data,1);
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
