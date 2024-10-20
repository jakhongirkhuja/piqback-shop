<?php

namespace App\Http\Controllers\Api\v1\Quizzes;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Lessons\Lesson;
use App\Models\Quizzes\QuizLog;
use App\Models\Quizzes\Quizz;
use App\Models\User;



use App\Services\QuizDashService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function quizList()
    {
        $self = request()->self;
        if($self){
            $lessons = Quizz::with('questions.variants','lesson')->where('id', $self)->first();
            return response()->json($lessons, Response::HTTP_OK);
        }
        $lesson_id = request()->id;
        if($lesson_id){
            $lessons = Quizz::with('questions.variants','lesson')->where('lesson_id', $lesson_id)->get();

        }else{
            if(request()->search){
                $lessons = Quizz::with('questions.variants', 'lesson')->whereHas('lesson',function($q){
                    $q->where('lessonTitleName->ru', 'ilike', '%'.request()->search.'%')
                    ->orwhere('lessonTitleName->uz', 'ilike', '%'.request()->search.'%');
                })->orderby('id','desc')->paginate(20);
            }else{
                $lessons = Quizz::with('questions.variants', 'lesson')->latest()->paginate(30);
            }

        }
       
        return response()->json($lessons, Response::HTTP_OK);
    }
    public function quizAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id'=>'required',
            'quiz'=>'required|json',
            'type'=>'required',
            'prizeStatus'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $quizzes = json_decode($request->quiz,true);
        
        $data = $request->all();
        if($data['type']=='with limited reward'){
            $validator = Validator::make($request->all(), [
                'prizeLimit'=>'required|numeric|min:1|max:9999',
            ]);
            if ($validator->fails()) {
                return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
            }
        }

        $quizz = Quizz::where('lesson_id', $data['lesson_id'])->first();
        if($quizz){
            return ErrorHelperResponse::returnError('Quiz is already exist in Lesson with given id',Response::HTTP_FOUND);
        }
        $lesson = Lesson::find($data['lesson_id']);
        if($lesson){
            try {
                $res =DB::transaction(function () use ($lesson, $quizzes, $data){
                    $quizz = new Quizz();
                    $quizz->saveModel($lesson->id, $quizzes, $data);
                    $responseArr['quizz'] =Quizz::with('questions', 'questions.variants')->where('id',$quizz->id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Lesson with given id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function quizedit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id'=>'required',
            'quiz_id'=>'required',
            'prizeIQC'=>'required',
            'timeLimits'=>'required',
            'numberRightAnswersToPass'=>'required',
            'type'=>'required',
            'prizeStatus'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if($data['type']=='with limited reward'){
            $validator = Validator::make($request->all(), [
                'prizeLimit'=>'required|numeric|min:1|max:9999',
            ]);
            if ($validator->fails()) {
                return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
            }
        }
        $quizz = Quizz::find($data['quiz_id']);
        $lesson = Lesson::find($data['lesson_id']);
        if($quizz && $lesson){
            try {
                $res =DB::transaction(function () use ($quizz,$data){
                    $quizz->editModel($data);
                    $responseArr['quizz'] =Quizz::with('questions', 'questions.variants')->where('id',$quizz->id)->first();;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Quiz or Lesson with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function quizDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quiz_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $quizz = Quizz::find($data['quiz_id']);
        if($quizz){
            if($quizz->questions->count()==0){
                try {
                    $res =DB::transaction(function () use ($quizz,$data){
                        $quizz->deleteModel($data);
                        $responseArr['message'] = 'Deleted';
                        return response()->json($responseArr, Response::HTTP_OK);
                    });
                    return $res;
                } catch (\Throwable $th) {
                    return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }else{
                return ErrorHelperResponse::returnError('First delete questions of Quizz',Response::HTTP_FOUND);
            }
            
            
        }else{
            return ErrorHelperResponse::returnError('Quiz or Lesson with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function logs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'quiz_id'=>'required',
            'quizAttempt'=>'required|numeric|min:1',
            'numberOfRightAnswers'=>'required|numeric|min:0',
            'timeLeft'=>'required|date_format:H:i:s'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid', $data['user_id'])->first();
        if(!$user){
            return ErrorHelperResponse::returnError('User with given id not found',Response::HTTP_NOT_FOUND);
        }
       
        $quizz = Quizz::where('id',$data['quiz_id'])->first();
        
        if(!$quizz){
            return ErrorHelperResponse::returnError('Quizz with given id not found',Response::HTTP_NOT_FOUND);
        }
        $quizLog = QuizLog::where('quiz_id',$data['quiz_id'])->where('user_id',$user->id)->first();
        if(!$quizLog){
            $quizLog = new QuizLog();
        }
        try {
            $res = DB::transaction(function () use ($quizLog, $user, $data){
                $quizLog->saveOrUpdate($user->id, $data);
                $responseArr['quiz_log'] = $quizLog;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function quizDash(QuizDashService $quizDash)
    {
        return $quizDash->showResponse();
    }
}
