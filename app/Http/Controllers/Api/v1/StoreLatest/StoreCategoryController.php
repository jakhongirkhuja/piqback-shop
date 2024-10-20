<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\StoreLatest\Category;
use App\Models\StoreLatest\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreCategoryController extends Controller
{
    public function categoryList()
    {
        $category_id = request()->category_id;
        $pagination = request()->paginate;
        if($category_id){
            $category = Category::find($category_id);
            if($category){
                return response()->json($category, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Category with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(Category::latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function categorySubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ru'=>'required',
            'name_uz'=>'required',
            'global_type'=>'required',
            'icon'=>'required|image|mimes:jpg,png,jpeg,svg|max:2048',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $category = Category::where('name->ru', $data['name_ru'])->where('name->uz', $data['name_uz'])->first();
        
        if($category){
            return ErrorHelperResponse::returnError('Category with given name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $category = new Category();
                $category->saveModel($data, 'Created');
                $responseArr['category'] =$category;
                $responseArr['message'] = 'Category has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function categoryEditSubmit(Request $request, $category_id)
    {
        $validator = Validator::make($request->all(), [
            'name_ru'=>'required',
            'name_uz'=>'required',
            'global_type'=>'required',
            'icon'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $category = Category::find($category_id);
        if(!$category){
            return ErrorHelperResponse::returnError('category with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $categoryCheckName = Category::where('id','!=',$category_id)->where(function ($query) use ($data) {
            $query->where('name->ru', $data['name_ru'])
                  ->orwhere('name->uz', $data['name_uz']);
        })->first();
        if($categoryCheckName){
            return ErrorHelperResponse::returnError('Category with given title name is  exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($category,$data){
               
                $category->updateModel($data, 'updated');
                $responseArr['category'] =$category;
                $responseArr['message'] = 'category has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function categoryDelete(Request $request, $category_id)
    {
        $category = Category::find($category_id);
        if(!$category){
            return ErrorHelperResponse::returnError('Category with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $store = Store::where('category_id',$category_id)->first();
        if($store){
            return ErrorHelperResponse::returnError('Store with given category is  exist, please first remove Store',Response::HTTP_FOUND);
        }
        $data = $request->all();
        if($category){
            try {
                $res = DB::transaction(function () use ($category, $data){
                    $category->deleteModel($data,'removed');
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
