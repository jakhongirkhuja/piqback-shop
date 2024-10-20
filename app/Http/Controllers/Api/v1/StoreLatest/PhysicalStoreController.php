<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Store\PhysicalStoreProduct;
use App\Models\Store\PhysicalStory;
use App\Models\StoreLatest\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PhysicalStoreController extends Controller
{
    public function physicalStoreList()
    {
        
        $store_id = request()->store_id;
        $pagination = request()->paginate;
        
        if($store_id){
            $store = PhysicalStory::find($store_id);
            if($store){
                return response()->json($store, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Physical Store  with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(PhysicalStory::with('category')->latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function physicalStoreSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'owner'=>'required',
            'longitude'=>'required',
            'latitude'=>'required',
            'landmark_ru'=>'required',
            'landmark_uz'=>'required',
            'description_ru'=>'required',
            'description_uz'=>'required',
            'mainContact'=>'required',
            'extraContact'=>'required',
            'name'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeMagazin = Category::find($data['category_id']);
        if(!$storeMagazin){
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
        $physicalStory = PhysicalStory::where('name', 'ILIKE',$data['name'])->first();
        if($physicalStory){
            return ErrorHelperResponse::returnError('Physical Store  with given  name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $store = new PhysicalStory();
                $store->saveModel($data, 'Created');
                $responseArr['PhysicalStore'] =$store;
                $responseArr['message'] = 'Physical Store has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function physicalStoreEditSubmit(Request $request, $store_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'owner'=>'required',
            'longitude'=>'required',
            'latitude'=>'required',
            'landmark_ru'=>'required',
            'landmark_uz'=>'required',
            'description_ru'=>'required',
            'description_uz'=>'required',
            'mainContact'=>'required',
            'extraContact'=>'required',
            'name'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeMagazin = Category::find($data['category_id']);
        if(!$storeMagazin){
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
        
        $physicalStory = PhysicalStory::find($store_id);
        if(!$physicalStory){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $physicalStoryNameCheck = PhysicalStory::where('id','!=', $store_id)->where('name', 'ILIKE',$data['name'])->first();
        if($physicalStoryNameCheck){
            return ErrorHelperResponse::returnError('Physical Store  with given  name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($physicalStory,$data){
                $physicalStory->saveModel($data, 'updated');
                $responseArr['PhysicalStore'] =$physicalStory;
                $responseArr['message'] = 'Physical Story has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function physicalStoreDelete(Request $request, $store_id)
    {
        $physicalStory = PhysicalStory::find($store_id);
        if(!$physicalStory){
            return ErrorHelperResponse::returnError('Physical Story with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($physicalStory){
            try {
                $res = DB::transaction(function () use ($physicalStory, $data){
                    $physicalStory->deleteModel($data,'deleted');
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public  function physicalStoreProductImageDelete(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'imageName'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $product = PhysicalStoreProduct::find($product_id);
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


    public function physicalStoreProductList()
    {
        
        $product_id = request()->product_id;
        $pagination = request()->paginate;
        
        if($product_id){
            $physicalStoreProduct = PhysicalStoreProduct::find($product_id);
            if($physicalStoreProduct){
                return response()->json($physicalStoreProduct, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Physical Store Product  with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(PhysicalStoreProduct::latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function physicalStoreProductSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'store_id'=>'required',
            'price'=>'required',
            'amount'=>'required',
            'ordered'=>'required',
            'name_uz'=>'required',
            'name_ru'=>'required',
            'description_uz'=>'required',
            'description_ru'=>'required',
            
            'img'=>'required',
            'img.*'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $category = Category::find($data['category_id']);
        if(!$category){
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
        $physicalStory = PhysicalStory::find($data['store_id']);
        if(!$physicalStory){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $store = new PhysicalStoreProduct();
                $store->saveModel($data, 'created');
                $responseArr['PhysicalStoreProduct'] =$store;
                $responseArr['message'] = 'Physical Store Product has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function physicalStoreProductEditSubmit(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'store_id'=>'required',
            'price'=>'required',
            'amount'=>'required',
            'ordered'=>'required',
            'name_uz'=>'required',
            'name_ru'=>'required',
            'description_uz'=>'required',
            'description_ru'=>'required',
            'img.*'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeMagazin = Category::find($data['category_id']);
        if(!$storeMagazin){
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
        
        $physicalStory = PhysicalStory::find($data['store_id']);
        if(!$physicalStory){
            return ErrorHelperResponse::returnError('Physical Story with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $physicalStoreProduct = PhysicalStoreProduct::find( $product_id);
        if(!$$physicalStoreProduct){
            return ErrorHelperResponse::returnError('Physical Story Product with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $physicalStoryNameCheck = PhysicalStoreProduct::where('id','!=', $product_id)->where(function ($query) use ($data) {
            $query->where('name->ru', strtoupper($data['name_ru']))
                  ->orwhere('name->uz', strtoupper($data['name_uz']));
        })->first();
        if($physicalStoryNameCheck){
            return ErrorHelperResponse::returnError('Physical Store Product  with given  name found',Response::HTTP_FOUND);
        }
        
        try {
            $res =DB::transaction(function () use ($physicalStoreProduct,$data){
                $physicalStoreProduct->updateModel($data, 'updated');
                $responseArr['PhysicalStoreProduct'] =$physicalStoreProduct;
                $responseArr['message'] = 'Physical Story Product has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function physicalStoreProductDelete(Request $request, $product_id)
    {
        $physicalStoreProduct = PhysicalStoreProduct::find($product_id);
        if(!$physicalStoreProduct){
            return ErrorHelperResponse::returnError('Physical Store Product with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($physicalStoreProduct){
            try {
                $res = DB::transaction(function () use ($physicalStoreProduct, $data){
                    $physicalStoreProduct->deleteModel($data,'deleted');
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
