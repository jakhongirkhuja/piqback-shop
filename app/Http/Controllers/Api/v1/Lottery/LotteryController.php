<?php

namespace App\Http\Controllers\Api\v1\Lottery;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Lottery\Lottery;
use App\Models\Lottery\LotteryLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LotteryController extends Controller
{
    public function lotteryList(){
        $self = request()->lottery_id;
        if($self){
            if(request()->logs){

                $lotteryLogs = LotteryLog::where('lottery_id',$self)->orderby('order','desc')->get();
                $newArray  =[];
                $newArray2 = [];
                foreach ($lotteryLogs as $key => $lotteryLog) {
                    $s = [];
                    $s['ticket']= $lotteryLog->ticket;
                    $s['datetime']=$lotteryLog->datetime;
                    $s['order']=$lotteryLog->order;
                    $s['hasWon']=$lotteryLog->hasWon;
                    $user = User::with('phonebook')->find($lotteryLog->user_id);
                    if($user){
                        $s['firstName'] = $user->firstName;
                        $s['lastName'] = $user->lastName;
                        $s['phoneNumber'] = $user->phonebook?->phoneNumber;
                    }
                    $newArray2[] = $s;
                    $s['user_id'] =$lotteryLog->user_id;
                    $s['id'] =$lotteryLog->id; 
                    $newArray[] = $s;
                }
                $lottery['lottery'] = Lottery::where('id', $self)->first();
                $lottery['logs'] = $newArray;
                $lottery['export'] = $newArray2;
                return response()->json($lottery, Response::HTTP_OK);
                
            }
            $lottery = Lottery::where('id', $self)->first();
            return response()->json($lottery, Response::HTTP_OK);
        }
       
        if(request()->search){
            $lottery = Lottery::where(function($q){
                $q->where('name', 'ilike', '%'.request()->search.'%');
            })->orderby('id','desc')->paginate(50);
            return response()->json($lottery, Response::HTTP_OK);
        }
        
        $lottery = Lottery::with('lotteryLogs')->latest()->paginate(50);
        return response()->json($lottery, Response::HTTP_OK);
    }
    public function lotteryAdd(Request $request){
        $validator = Validator::make($request->all(), [
            'course_id'=>'required',
            'startDate'=>'required',
            'endDate'=>'required',
            'limit'=>'required|min:1|max:99999',
            'name'=>'required',
            'description'=>'required',
            'deadline'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $course = Course::find($data['course_id']);
        if(!$course) return ErrorHelperResponse::returnError('Course  with given id not found',Response::HTTP_NOT_FOUND);
        if(Lottery::where('course_id', $data['course_id'])->first()) return ErrorHelperResponse::returnError('Course  with given id has already benn linked to Lottery',Response::HTTP_NOT_FOUND);
        try {
            $res =DB::transaction(function () use ($data){
                $lottery = new Lottery();
                $lottery->saveModel($data, 'created');
                $responseArr['question'] =$lottery;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function lotteryEdit(Request $request){
        $validator = Validator::make($request->all(), [
            'lottery_id'=>'required',
            'course_id'=>'required',
            'startDate'=>'required',
            'endDate'=>'required',
            'limit'=>'required|min:1|max:99999',
            'name'=>'required',
            'description'=>'required',
            'deadline'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $lottery= Lottery::find($data['lottery_id']);
        if(!$lottery) return ErrorHelperResponse::returnError('Lottery  with given id not found',Response::HTTP_NOT_FOUND);
        if(!Course::find($data['course_id'])) return ErrorHelperResponse::returnError('Course  with given id not found',Response::HTTP_NOT_FOUND);
        $letterySearch = Lottery::where('course_id', $data['course_id'])->first();
        if($letterySearch->id!=$lottery->id) return ErrorHelperResponse::returnError('Course  with given id  is exist in lottery',Response::HTTP_NOT_FOUND);
        try {
            $res =DB::transaction(function () use ($lottery, $data){
                $lottery->saveModel($data, 'updated');
                $responseArr['question'] =$lottery;
                $responseArr['message'] = 'Updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function lotteryDelete(Request $request){
        $validator = Validator::make($request->all(), [
            'lottery_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $lottery= Lottery::find($data['lottery_id']);
        if(!$lottery) return ErrorHelperResponse::returnError('Lottery  with given id not found',Response::HTTP_NOT_FOUND);
        try {
            $res =DB::transaction(function () use ($lottery,$data){
                $lottery->deleteModel($data, 'deleted');
                $responseArr['question'] =$lottery;
                $responseArr['message'] = 'Deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function updateStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'lottery_log_id'=>'required',
            'user_id'=>'required',
            'status'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $lottery= LotteryLog::where('id',$data['lottery_log_id'])->where('user_id', $data['user_id'])->first();
        if(!$lottery) return ErrorHelperResponse::returnError('LotteryLog  with given id not found',Response::HTTP_NOT_FOUND);
        try {
            $res =DB::transaction(function () use ($lottery,$data){
                $lottery->updateStatus($data);
                $responseArr['question'] =$lottery;
                $responseArr['message'] = 'Updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
