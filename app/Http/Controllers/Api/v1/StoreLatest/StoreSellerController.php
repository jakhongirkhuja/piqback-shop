<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\StoreLatest\SellerTelegram;
use App\Models\StoreLatest\StoreSeller;
use App\Models\StoreLatest\StoreSellerReport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreSellerController extends Controller
{
    public function storeSellerList()
    {
        $storeseller_id = request()->seller_id;
        
        $pagination = request()->paginate;
        if($storeseller_id){
            $category = StoreSeller::with('sellerStoreList.store','telegram')->find($storeseller_id);
            if($category){
                return response()->json($category, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Seller with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(StoreSeller::with('sellerStoreList.store','telegram')->latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function storeSellerSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sellerName'=>'required',
            'sellerPhone'=>'required|size:12',
            'role'=>'required|boolean',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        $storeSellerPhoneCheck = StoreSeller::where('sellerPhone', $data['sellerPhone'])->first();
        if($storeSellerPhoneCheck){
            return ErrorHelperResponse::returnError('Seller with given phone number is  exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                
                $storeSeller = new StoreSeller();
                $storeSeller->saveModel($data, 'Created');
                $responseArr['storeseller'] =$storeSeller;
                $responseArr['message'] = 'Store Seller has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeSellerEditSubmit(Request $request, $storeseller_id)
    {
        $validator = Validator::make($request->all(), [
            'sellerName'=>'required',
            'sellerPhone'=>'required|size:12',
            'role'=>'required|boolean',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeSeller = StoreSeller::find($storeseller_id);
        if(!$storeSeller){
            return ErrorHelperResponse::returnError('Seller with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeSellerPhoneCheck = StoreSeller::where('id','!=',$storeseller_id)->where('sellerPhone', $data['sellerPhone'])->first();
        if($storeSellerPhoneCheck){
            return ErrorHelperResponse::returnError('Seller with given phone number is  exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($storeSeller,$data){
               
                $storeSeller->updateModel($data, 'updated');
                $responseArr['storeSeller'] =$storeSeller;
                $responseArr['message'] = 'Seller has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeSellerDelete(Request $request, $storeseller_id)
    {
        $storeSeller = StoreSeller::find($storeseller_id);
        if(!$storeSeller){
            return ErrorHelperResponse::returnError('Store Seller with given id is not exist',Response::HTTP_NOT_FOUND);
        }
         
        $data = $request->all();
        if($storeSeller){
            try {
                $res = DB::transaction(function () use ($storeSeller, $data,$storeseller_id){
                    $storeSellerTelegram = SellerTelegram::where('seller_id',$storeSeller->id)->first();
                    if($storeSellerTelegram){
                        $storeSellerTelegram->deleteModel($data,1);
                    }
                    $storeSeller->deleteModel($data,'removed');
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    
    public function reports()
    {
        $response['reports']=StoreSellerReport::with('storeproductcode.product','seller')->latest()->get();
        return response()->json($response, Response::HTTP_OK);
    }
}
