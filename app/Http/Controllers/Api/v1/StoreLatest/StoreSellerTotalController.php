<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\StoreLatest\Store;
use App\Models\StoreLatest\StoreSeller;
use App\Models\StoreLatest\StoreSellerList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreSellerTotalController extends Controller
{
    public function storeSellerTotalList()
    {
        $store_id = request()->store_id;
       
        if($store_id){
            $storeSellerList = StoreSellerList::with('seller','store')->where('store_id',$store_id)->get();
            if($storeSellerList){
                return response()->json($storeSellerList, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Store id not given',Response::HTTP_NOT_FOUND);
            }
        }
        return ErrorHelperResponse::returnError('Seller with given id not found',Response::HTTP_NOT_FOUND);
    }
    public function storeSellerSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id'=>'required|numeric',
            'seller_id'=>'required|numeric',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        $storeCheck = Store::find($data['store_id']);
        if(!$storeCheck){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeSellerCheck = StoreSeller::find($data['seller_id']);
        if(!$storeSellerCheck){
            return ErrorHelperResponse::returnError('Store seller with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeSellerListCheck = StoreSellerList::where('seller_id', $data['seller_id'])->first();
        if($storeSellerListCheck){
            return ErrorHelperResponse::returnError('Seller with given id is  exist in Store Seller List',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                
                $storeSellerList = new StoreSellerList();
                $storeSellerList->saveModel($data, 0);
                $responseArr['storesellerList'] =$storeSellerList;
                $responseArr['message'] = 'Store Seller has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeSellerEditSubmit(Request $request, $storesellerList_id)
    {
        $validator = Validator::make($request->all(), [
            'store_id'=>'required|numeric',
            'seller_id'=>'required|numeric',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeCheck = Store::find($data['store_id']);
        if(!$storeCheck){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeSellerCheck = StoreSeller::find($data['seller_id']);
        if(!$storeSellerCheck){
            return ErrorHelperResponse::returnError('Store seller with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeSellerListCheck = StoreSellerList::where('seller_id', $data['seller_id'])->first();
        if($storeSellerListCheck){
            return ErrorHelperResponse::returnError('Seller with given id is  exist in Store Seller List',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($storesellerList_id,$data){
               
                // $storeSellerList = new StoreSellerList();
                // $storeSellerList->saveModel($data, 0);
                $responseArr['storesellerList'] =$storesellerList_id;
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeSellerTotalDelete(Request $request, $storeSellerList_id)
    {
        $storeSeller = StoreSellerList::find($storeSellerList_id);
        if(!$storeSeller){
            return ErrorHelperResponse::returnError('Store Seller List with given id is not exist',Response::HTTP_NOT_FOUND);
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
