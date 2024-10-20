<?php

namespace App\Http\Controllers\Api\v1\News;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\NewsModel\NewsModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class NewsController extends Controller
{
    public function newslist()
    {
        $id = request()->id;
        if($id){
            $lessons = NewsModel::find($id);
            return response()->json($lessons, Response::HTTP_OK);
        }
        $news = NewsModel::latest()->paginate(100);
        return response()->json($news, Response::HTTP_OK);
    }
    public function newsAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_ru'=>'required',
            'title_uz'=>'required',
            'content_ru'=>'required',
            'content_uz'=>'required',
            'banner'=>'required|image|max:1024',
            'postedDate'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $res =DB::transaction(function () use ($data){
                $news = new NewsModel();
                $news->saveModel($data);
                $responseArr['news'] =$news;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function newsEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title_ru'=>'required',
            'title_uz'=>'required',
            'content_ru'=>'required',
            'content_uz'=>'required',
            'banner'=>'image|max:1024',
            'postedDate'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $newsModel = NewsModel::find($id);
        if(!$newsModel){
            return ErrorHelperResponse::returnError('Not found',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        try {
            $res =DB::transaction(function () use ($data,$newsModel){
               
                $newsModel->updateModel($data);
                $responseArr['news'] =$newsModel;
                $responseArr['message'] = 'Updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function newsDelete(Request $request, $id)
    {
        $newsModel = NewsModel::find($id);
        if(!$newsModel){
            return ErrorHelperResponse::returnError('Not found',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        try {
            $res =DB::transaction(function () use ($data, $newsModel){
               
                $newsModel->deleteModel($data);
                $responseArr['news'] =$newsModel;
                $responseArr['message'] = 'Deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
