<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Store\DigitalStore;
use App\Models\Store\PhysicalStory;
use App\Models\StoreLatest\Category;
use App\Models\StoreLatest\Store;
use App\Models\StoreLatest\StoreProduct;
use App\Models\User;
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
            $store = Store::find($store_id);
            if($store){
                return response()->json($store, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Store with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(Store::with('category')->latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function getStoresByType(){
        if(request()->storeType=='physical'){
            return response()->json(PhysicalStory::latest()->paginate(100), Response::HTTP_OK);
        }
        return response()->json(DigitalStore::latest()->paginate(100), Response::HTTP_OK);
        
    }
    public function storeOwner()
    {
        
        return response()->json(User::with('phonebook')->where('role','Store Owner')->latest()->paginate(100), Response::HTTP_OK);
        
    }
    public function storeSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required|numeric',
            'storeOwner'=>'required|numeric',
            'storeName'=>'required',
            'storeLongitude'=>'required',
            'storeLatitude'=>'required',
            'storeLandmark_ru'=>'required',
            'storeLandmark_uz'=>'required',
            'storeDescription_ru'=>'required',
            'storeDescription_uz'=>'required',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $category = Category::find($data['category_id']);
        if(!$category){
            return ErrorHelperResponse::returnError('Category with given  id not found',Response::HTTP_NOT_FOUND);
        }
        $storeOwner = User::where('role','Store Owner')->where('id',$data['storeOwner'])->first();
        if(!$storeOwner){
            return ErrorHelperResponse::returnError('User with given  id or role not found',Response::HTTP_NOT_FOUND);
        }
        $store = Store::where('storeName', $data['storeName'])->first();
        if($store){
            return ErrorHelperResponse::returnError('Store with given  name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $store = new Store();
                $store->saveModel($data, 'created');
                $responseArr['store'] =$store;
                $responseArr['message'] = 'Store has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeEditSubmit(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required|numeric',
            'storeOwner'=>'required|numeric',
            'storeName'=>'required',
            'storeLongitude'=>'required',
            'storeLatitude'=>'required',
            'storeLandmark_ru'=>'required',
            'storeLandmark_uz'=>'required',
            'storeDescription_ru'=>'required',
            'storeDescription_uz'=>'required',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $store = Store::find($product_id);
        if(!$store){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        
        $storeCheckTitle = Store::where('id','!=',$product_id)->where('storeName',$data['storeName'])->first();
        if($storeCheckTitle){
            return ErrorHelperResponse::returnError('Store with given  name is  exist',Response::HTTP_FOUND);
        }
        $category = Category::find($data['category_id']);
        if(!$category){
            return ErrorHelperResponse::returnError('Category with given  id not found',Response::HTTP_NOT_FOUND);
        }
        $storeOwner = User::where('role','Store Owner')->where('id',$data['storeOwner'])->first();
        if(!$storeOwner){
            return ErrorHelperResponse::returnError('User with given  id or role not found',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($store,$data){
               
                $store->saveModel($data, 'updated');
                $responseArr['store'] =$store;
                $responseArr['message'] = 'Store has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeDelete(Request $request, $product_id)
    {
        $store = Store::find($product_id);
        if(!$store){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeProduct = StoreProduct::where('store_id',$store->id)->first();
        if($storeProduct){
            return ErrorHelperResponse::returnError('Store Product exist, please first change category of Store Product',Response::HTTP_FOUND);
        }
        $data = $request->all();
        if($store){
            try {
                $res = DB::transaction(function () use ($store, $data){
                    $store->deleteModel($data,'removed');
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
