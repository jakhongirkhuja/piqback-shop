<?php

namespace App\Http\Controllers\Api\v1\Wish;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\User;
use App\Models\Wish\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WishController extends Controller
{
    public function wishList()
    {
        $user_id = request()->user_id;
        if($user_id){
            $user = User::where('hrid', $user_id)->first();
            if($user){
                return response()->json(Wishlist::where('user_id', $user->id)->all(), Response::HTTP_CREATED);
            }else{
                return ErrorHelperResponse::returnError('User with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(Wishlist::paginate(10), Response::HTTP_CREATED);
        }
    }
    public function wishAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'course_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid', $data['user_id'])->first();
        if(!$user){
            return ErrorHelperResponse::returnError('User with given id not found',Response::HTTP_NOT_FOUND);
        }
        $wishList = Wishlist::where('user_id', $user->id)->where('course_id', $data['course_id'])->first();
        if($wishList){
            return ErrorHelperResponse::returnError('Course is exist in wishlist',Response::HTTP_FOUND);
        }
        $course = Course::find($data['course_id']);
        if($course){
            try {
                $res =DB::transaction(function () use ($user, $data){
                    $wishlist = new Wishlist();
                    $wishlist->saveModel($user,$data);
                    $responseArr['wishlist'] =$wishlist;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_CREATED);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course with given id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function wishEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wish_id'=>'required',
            'status'=>'required'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $wishList = Wishlist::where('id',$data['wish_id'])->first();
        if(!$wishList){
            return ErrorHelperResponse::returnError('WishList id is not exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($wishList, $data){
                $wishList->updateModel($data, $data['status']);
                $responseArr['message'] = $data['status'];
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function wishDelete(Request $request)
    {
        
    }
}
