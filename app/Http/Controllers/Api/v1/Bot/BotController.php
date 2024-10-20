<?php

namespace App\Http\Controllers\Api\v1\Bot;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Phonebook;
use App\Models\StoreLatest\SellerTelegram;
use App\Models\StoreLatest\SellerTelegramHistory;
use App\Models\StoreLatest\Store;
use App\Models\StoreLatest\StoreProduct;
use App\Models\StoreLatest\StoreProductCode;
use App\Models\StoreLatest\StoreSeller;
use App\Models\StoreLatest\StoreSellerList;
use App\Models\StoreLatest\StoreSellerReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BotController extends Controller
{
    
    public function botRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sellerPhone'=>'required|size:12',
            'telegram_id'=>'required'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();

        $phoneNumbers = Phonebook::where('phoneNumber',$data['sellerPhone'])->first();
        if($phoneNumbers){
            $user = User::find($phoneNumbers->user_id)->makeHidden(['id','created_at','updated_at']);
            if($user && $user->role=='Store Owner'){
                $responseArr['store']= Store::where('storeOwner', $user->id)->first();
                $responseArr['user']=$user;
                $responseArr['role']='Store Owner';
                return response()->json($responseArr, Response::HTTP_OK);
            }
        }
        $storeSellerPhoneCheck = StoreSeller::where('sellerPhone', $data['sellerPhone'])->first();
        if(!$storeSellerPhoneCheck){
            return ErrorHelperResponse::returnError('Seller with given phone number is not  exist',Response::HTTP_FOUND);
        }
        
        $sellerTelegram = SellerTelegram::where('seller_id', $storeSellerPhoneCheck->id)->first();
        if($sellerTelegram){
            if($sellerTelegram->telegram_id==$data['telegram_id']){
                $storeSellers = StoreSellerList::where('seller_id', $storeSellerPhoneCheck->id )->first();
                $storeInfo = [];
                if($storeSellers){
                    $store = Store::find($storeSellers->store_id);
                    if($store){
                        $storeInfo = $store;
                    }
                }
                $responseArr['store'] =$storeInfo;
                $responseArr['user'] = $storeSellerPhoneCheck;
                $responseArr['role']='Seller';
                return response()->json($responseArr, Response::HTTP_OK);
            }
            return ErrorHelperResponse::returnError('Seller telegram id  is not assigned to this phoneNumber ',Response::HTTP_NOT_FOUND);
        }else{
            try {
                $res =DB::transaction(function () use ($data, $storeSellerPhoneCheck){
                    $storeSeller = new SellerTelegram();
                    $storeSeller->saveModelBot($data, 0,$storeSellerPhoneCheck->id);
                   

                    $storeSellers = StoreSellerList::where('seller_id', $storeSellerPhoneCheck->id )->first();
                    $storeInfo = [];
                    if($storeSellers){
                        $store = Store::find($storeSellers->store_id);
                        if($store){
                            $storeInfo = $store;
                        }
                    }
                    $responseArr['store'] =$storeInfo;
                    $responseArr['user'] = $storeSellerPhoneCheck;
                    $responseArr['role']='Seller';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
    }
    public function checkHash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hash'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeProductCode = StoreProductCode::where('productCode',$data['hash'])->first();
        if($storeProductCode){
            $storeSellerReport = StoreSellerReport::with('seller')->where('product_code_id', $storeProductCode->id)->latest()->first();
            $user = User::with('phonebook')->where('id',$storeProductCode->user_id)->first();
            $product = StoreProduct::find($storeProductCode->product_id);
            $responseArr['order'] =$storeProductCode? $storeProductCode->makeHidden(['product_id','user_id','productCode','created_at','updated_at']) : null;
            $responseArr['report'] = $storeSellerReport;
            $responseArr['client'] = $user? $user->makeHidden(['created_at','updated_at']) : null;
            $responseArr['product'] = $product? $product->makeHidden(['created_at','updated_at']) : null;
            return response()->json($responseArr, Response::HTTP_OK);
        }else{
            return ErrorHelperResponse::returnError('Order not found in databse',Response::HTTP_NOT_FOUND);
        }
    }
    public function submitReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_code_id'=>'required|numeric',
            'seller_id'=>'required|numeric',
            'action'=>'required|boolean',
            'reportIMG'=>'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
            'shortDescription'=>'nullable',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeProductCode = StoreProductCode::find($data['product_code_id']);
        if(!$storeProductCode){
            return ErrorHelperResponse::returnError('StoreProduct with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeSellerReport = StoreSellerReport::where('product_code_id',$data['product_code_id'])->first();
        if($storeSellerReport && $storeSellerReport->action){
            return ErrorHelperResponse::returnError('Product has already been given',Response::HTTP_FOUND);
        }
        $store = StoreProduct::find($storeProductCode->product_id);
        if(!$store){
            return ErrorHelperResponse::returnError('StoreProduct with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $user = false;
        if(request()->hrid){
            $user = User::where('hrid',request()->hrid )->first();
            if(!$user){
                return ErrorHelperResponse::returnError('Store Seller Owners with given id is not exist',Response::HTTP_NOT_FOUND);
            }
        }else{
            $store = StoreSeller::find($data['seller_id']);
            if(!$store){
                return ErrorHelperResponse::returnError('Store Seller with given id is not exist',Response::HTTP_NOT_FOUND);
            }
        }
        try {
            $res =DB::transaction(function () use ($storeSellerReport, $data,$user ){
                if($storeSellerReport){
                    $storeSellerReport->saveModel($data,$user);
                }else{
                    $storeSellerReport = new StoreSellerReport();
                    $storeSellerReport->saveModel($data,$user);
                }
                $responseArr['store'] =$storeSellerReport;
                $responseArr['message'] = 'Store Seller Report has been Send';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
