<?php

namespace App\Http\Controllers\Api\v1\Store;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Inbox\InboxMessage;
use App\Models\Money\Iqc;
use App\Models\Store\StoreObject;
use App\Models\Store\StoreObjectCode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    public function storeList()
    {
        $store_id = request()->store_id;
        $pagination = request()->paginate;
        if($store_id){
            $store = StoreObject::find($store_id);
            if($store){
                return response()->json($store, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('StoreObject with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(StoreObject::latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function storeSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'objectName_ru'=>'required',
            'objectName_uz'=>'required',
            'objectDescription_ru'=>'required',
            'objectDescription_uz'=>'required',
            'objectIMG'=>'required|image|mimes:jpg,png,jpeg,svg|max:2048',
            'objectCost'=>'required|numeric',
            'objectAmount'=>'required|numeric',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $store = StoreObject::where('objectName->ru', $data['objectName_ru'])->where('objectName->uz', $data['objectName_uz'])->first();
        if($store){
            return ErrorHelperResponse::returnError('Store with given title name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $store = new StoreObject();
                $store->saveModel($data, 'Created');
                $responseArr['store'] =$store;
                $responseArr['message'] = 'Store has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeEditSubmit(Request $request, $store_id)
    {
        $validator = Validator::make($request->all(), [
            'objectName_ru'=>'required',
            'objectName_uz'=>'required',
            'objectDescription_ru'=>'required',
            'objectDescription_uz'=>'required',
            'objectIMG'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            'objectCost'=>'required|numeric',
            'objectAmount'=>'required|numeric',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $store = StoreObject::find($store_id);
        if(!$store){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeCheckTitle = StoreObject::where('id','!=',$store_id)->where(function ($query) use ($data) {
            $query->where('objectName->ru', $data['objectName_ru'])
                  ->orwhere('objectName->uz', $data['objectName_uz']);
        })->first();
        if($storeCheckTitle){
            return ErrorHelperResponse::returnError('Store with given title name is  exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($store,$data){
               
                $store->updateModel($data, 'updated');
                $responseArr['store'] =$store;
                $responseArr['message'] = 'Store has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeDelete(Request $request, $store_id)
    {
        $store = StoreObject::find($store_id);
        if(!$store){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($store){
            try {
                $res = DB::transaction(function () use ($store, $data){
                    $store->deleteModel($data,'deactivated');
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public function buyObject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'object_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $store = StoreObject::find($data['object_id']);
        if(!$store){
            return ErrorHelperResponse::returnError('StoreObject with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $iqc =  Iqc::where('user_id', auth()->user()->id)->first();
        if($iqc){
            if($iqc->amountofIQC< $store->objectCost){
                return ErrorHelperResponse::returnError('You have not enough  IQC  to get',Response::HTTP_NOT_FOUND);
            }
        }else{
            return ErrorHelperResponse::returnError('You have not IQC  or equel to 0 ',Response::HTTP_NOT_FOUND);
        }
        $countOrder = 0;
        $storeObejctCode= StoreObjectCode::where('object_id', $data['object_id'])->orderby('objectOrderCount','desc')->first();
        if($storeObejctCode){
            $countOrder = (int) $storeObejctCode->objectOrderCount;
            $countOrder++;
        }else{
            $countOrder++;
        }
        try {
            $res =DB::transaction(function () use ($data, $countOrder,$iqc, $store){
                $storeObjectCode = new StoreObjectCode();
                $storeObjectCode->saveModel($data,$countOrder,$iqc,$store);
                $responseArr['store'] =$storeObjectCode;
                $responseArr['message'] = 'Store Object has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $iqc = new Iqc();
        // $iqc->saveModel($data, auth()->user()->id,$changedPrice,1,'storeObject', $store->id);
    }
    public function checkObjectCodeMarket(Request $request){
        $validator = Validator::make($request->all(), [
            'object_id'=>'required',
            'market_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $store = StoreObject::find($data['object_id']);
        if(!$store){
            return ErrorHelperResponse::returnError('StoreObject with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        return 'ok';
    }
}
