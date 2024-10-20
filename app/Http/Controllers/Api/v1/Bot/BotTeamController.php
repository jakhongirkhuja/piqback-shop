<?php

namespace App\Http\Controllers\Api\v1\Bot;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\StoreLatest\SellerTelegram;
use App\Models\StoreLatest\Store;
use App\Models\StoreLatest\StoreSeller;
use App\Models\StoreLatest\StoreSellerList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BotTeamController extends Controller
{
    public function showTeamList()
    {
        $user = User::where('hrid', request()->hrid)->where('role','Store Owner')->first();
        if($user){
            $store = Store::where('storeOwner',$user->id)->first();
           
            if($store){
                $storeSellerList = StoreSellerList::with('seller')->where('store_id',$store->id)->get()->makeHidden(['store_id','created_at','updated_at']);
                if($storeSellerList){
                    return response()->json($storeSellerList, Response::HTTP_OK);
                }else{
                    return ErrorHelperResponse::returnError('Store Seller not exist',Response::HTTP_NOT_FOUND);
                }
            }else{
                return ErrorHelperResponse::returnError('Store not exist',Response::HTTP_NOT_FOUND);
            }
        }
        return ErrorHelperResponse::returnError('Store  owner not found',Response::HTTP_NOT_FOUND);
    }
    public function teamAddSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sellerName'=>'required',
            'sellerPhone'=>'required|size:12',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $user = User::where('hrid', request()->hrid)->where('role','Store Owner')->first();
        if($user){
            $data = $request->all();
            $storeSellerPhoneCheck = StoreSeller::where('sellerPhone', $data['sellerPhone'])->first();
            if($storeSellerPhoneCheck){
                return ErrorHelperResponse::returnError('Seller with given phone number is  exist',Response::HTTP_FOUND);
            }
            
            $store = Store::where('storeOwner', $user->id)->first();
            if(!$store){
                return ErrorHelperResponse::returnError('Store of user owner is not exist',Response::HTTP_NOT_FOUND);
            }
            try {
                $res =DB::transaction(function () use ($data,$user, $store){
                    $storeSeller = new StoreSeller();
                    $storeSeller->saveModelBot($data, 'Created', $user->id);
                    $storeSellerList = new StoreSellerList();
                    $storeSellerList->saveModelBot($data, 0,$user->id, $store->id, $storeSeller->id);
                    $responseArr['seller'] =$storeSeller;
                    $responseArr['message'] = 'Store Seller has been created';
                    return response()->json($responseArr, Response::HTTP_CREATED);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return ErrorHelperResponse::returnError('Store  owner not found',Response::HTTP_NOT_FOUND);
    }
    public function teamDelete(Request $request, $team_id)
    {
        
        $user = User::where('hrid', request()->hrid)->where('role','Store Owner')->first();
        if($user){
            $store = Store::where('storeOwner', $user->id)->first();
            if(!$store){
                return ErrorHelperResponse::returnError('Store of user owner is not exist',Response::HTTP_NOT_FOUND);
            }
            $storeSellerList = StoreSellerList::where('id',$team_id)->where('store_id', $store->id)->first();
            if(!$storeSellerList){
                return ErrorHelperResponse::returnError('Store Seller List with given id is not exist or You are not owner',Response::HTTP_NOT_FOUND);
            }
            $data = $request->all();

            if($storeSellerList){
                try {
                    $res = DB::transaction(function () use ($storeSellerList, $data, $user){
                        $storeSellerList->deleteModelBot($data,1, $user->id);
                        $storeSeller = StoreSeller::find($storeSellerList->seller_id);
                        if($storeSeller){
                            $storeSellerTelegram = SellerTelegram::where('seller_id',$storeSeller->id)->first();
                            if($storeSellerTelegram){
                                $storeSellerTelegram->deleteModel($data,1);
                            }
                            $storeSeller->deleteModel($data,'removed');
                        }
                        $responseArr['message'] = 'Deleted';
                        return response()->json($responseArr, Response::HTTP_OK);
                    });
                    return $res;
                } catch (\Throwable $th) {
                    return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }
        return ErrorHelperResponse::returnError('Store  owner not found',Response::HTTP_NOT_FOUND);
    }
}
