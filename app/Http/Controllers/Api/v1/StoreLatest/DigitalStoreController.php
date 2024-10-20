<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Store\DigitalStore;
use App\Models\Store\DigitalStoreProduct;
use App\Models\StoreLatest\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DigitalStoreController extends Controller
{
    public function digitalStoreList()
    {
        
        $store_id = request()->store_id;
        $pagination = request()->paginate;
        
        if($store_id){
            $store = DigitalStore::find($store_id);
            if($store){
                return response()->json($store, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Digital Store  with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(DigitalStore::with('category')->latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function digitalStoreSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'name'=>'required',
            'logo'=>'required|image',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeMagazin = Category::find($data['category_id']);
        if(!$storeMagazin){
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
        $DigitalStore = DigitalStore::where('name', 'ILIKE',$data['name'])->first();
        if($DigitalStore){
            return ErrorHelperResponse::returnError('Digital Store  with given  name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $store = new DigitalStore();
                $store->saveModel($data, 'created');
                $responseArr['DigitalStore'] =$store;
                $responseArr['message'] = 'Digital Store has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function digitalStoreEditSubmit(Request $request, $store_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'name'=>'required',
            'logo'=>'image',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeCategory = Category::find($data['category_id']);
        if(!$storeCategory){
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
        
        $digitalStore = DigitalStore::find($store_id);
        if(!$digitalStore){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $digitalStoreNameCheck = DigitalStore::where('id','!=', $store_id)->where('name', 'ILIKE',$data['name'])->first();
        if($digitalStoreNameCheck){
            return ErrorHelperResponse::returnError('Digital Store  with given  name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($digitalStore,$data){
                $digitalStore->updateModel($data, 'updated');
                $responseArr['DigitalStore'] =$digitalStore;
                $responseArr['message'] = 'Digital Story has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function digitalStoreDelete(Request $request, $store_id)
    {
        $digitalStore = DigitalStore::find($store_id);
        if(!$digitalStore){
            return ErrorHelperResponse::returnError('Digital Story with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($digitalStore){
            try {
                $res = DB::transaction(function () use ($digitalStore, $data){
                    $digitalStore->deleteModel($data,'deleted');
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }



    public function digitalStoreProductList()
    {
        
        $product_id = request()->product_id;
        $pagination = request()->paginate;
        
        if($product_id){
            $digitalStoreProduct = DigitalStoreProduct::find($product_id);
            if($digitalStoreProduct){
                return response()->json($digitalStoreProduct, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Digital Store Product  with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(DigitalStoreProduct::latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function digitalStoreProductSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'store_id'=>'required',
            'price'=>'required',
            'amount'=>'required',
            'ordered'=>'required',
            'name_uz'=>'required',
            'name_ru'=>'required',
            'img'=>'required',
            'img.*'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        // $category = Category::find($data['category_id']);
        // if(!$category){
        //     return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        // }
        $DigitalStore = DigitalStore::find($data['store_id']);
        if(!$DigitalStore){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $store = new DigitalStoreProduct();
                $store->saveModel($data, 'created');
                $responseArr['DigitalStoreProduct'] =$store;
                $responseArr['message'] = 'Digital Store Product has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function digitalStoreProductEditSubmit(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'store_id'=>'required',
            'price'=>'required',
            'amount'=>'required',
            'ordered'=>'required',
            'name_uz'=>'required',
            'name_ru'=>'required',
            'img.*'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        // $storeMagazin = Category::find($data['category_id']);
        // if(!$storeMagazin){
        //     return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        // }
        
        $digitalStore = DigitalStore::find($data['store_id']);
        if(!$digitalStore){
            return ErrorHelperResponse::returnError('Digital Story with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $digitalStoreProduct = DigitalStoreProduct::find( $product_id);
        if(!$digitalStoreProduct){
            return ErrorHelperResponse::returnError('Digital Story Product with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $digitalStoreNameCheck = DigitalStoreProduct::where('id','!=', $product_id)->where(function ($query) use ($data) {
            $query->where('name->ru', strtoupper($data['name_ru']))
                  ->orwhere('name->uz', strtoupper($data['name_uz']));
        })->first();
        if($digitalStoreNameCheck){
            return ErrorHelperResponse::returnError('Digital Store Product  with given  name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($digitalStoreProduct,$data){
                $digitalStoreProduct->updateModel($data, 'updated');
                $responseArr['DigitalStore'] =$digitalStoreProduct;
                $responseArr['message'] = 'Digital Story has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function digitalStoreProductDelete(Request $request, $product_id)
    {
        $digitalStoreProduct = DigitalStoreProduct::find($product_id);
        if(!$digitalStoreProduct){
            return ErrorHelperResponse::returnError('Digital Store Product with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($digitalStoreProduct){
            try {
                $res = DB::transaction(function () use ($digitalStoreProduct, $data){
                    $digitalStoreProduct->deleteModel($data,'deleted');
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public  function digitalStoreProductImageDelete(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'imageName'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $product = DigitalStoreProduct::find($product_id);
        if(!$product){
            return ErrorHelperResponse::returnError('Product Store Product not found',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($product,$data){
               
                $product->deleteImage($data);
                $responseArr['product'] =$product;
                $responseArr['message'] = 'Image has been removed';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
