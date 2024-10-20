<?php

namespace App\Http\Controllers\Api\v1\Lesson;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonContent;
use App\Models\Lessons\LessonExtraMaterial;
use App\Models\Lessons\LessonLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function lessonList()
    {
        $self = request()->self;
        if($self){
            $lessons = Lesson::with('contents','course.infos','materials')->where('id', $self)->first();
            return response()->json($lessons, Response::HTTP_OK);
        }
       
        $course_id = request()->id;
        if($course_id){
            $lessons = Lesson::with('contents','course.infos')->where('course_id', $course_id)->latest()->paginate(100);
        }else{
            if(request()->search){
                $lessons = Lesson::with('course.infos')->where(function($q){
                    $q->where('lessonTitleName->ru', 'ilike', '%'.request()->search.'%')
                    ->orwhere('lessonTitleName->uz', 'ilike', '%'.request()->search.'%');
                })->orderby('id','desc')->paginate(20);
            }else{
                $lessons = Lesson::with('course.infos')->latest()->paginate(request()->show=='all'? 1000 : 100);
            }
        }
        
        return response()->json($lessons, Response::HTTP_OK);
    }
    public function lessonAdd(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'course_id'=>'required',
            'order'=>'required',
            'lessonTitleName_ru'=>'required',
            'lessonTitleName_uz'=>'required',
            'video_ru'=>'required',
            'video_uz'=>'required',
            'videoLength_ru'=>'required|numeric',
            'videoLength_uz'=>'required|numeric',
            'contents'=>'nullable|json',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        $course = Course::find($data['course_id']);
        if($course){
            try {
                $res =DB::transaction(function () use ($data){
                    $lesson = new Lesson();
                    $lesson->saveModel($data);
                    $responseArr['lesson'] =Lesson::with('contents')->where('id',$lesson->id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course with given id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function lessonEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
            'lesson_id'=>'required',
            'course_id'=>'required',
            'order'=>'required',
            'lessonTitleName_ru'=>'required',
            'lessonTitleName_uz'=>'required',
            'video_ru'=>'required',
            'video_uz'=>'required',
            'videoLength_ru'=>'required|numeric',
            'videoLength_uz'=>'required|numeric',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $course = Course::find($data['course_id']);
        $lesson = Lesson::find($data['lesson_id']);
        if($course && $lesson){
            try {
                $res =DB::transaction(function () use ($lesson, $data){
                    $lesson->updateModel($data);
                    $responseArr['lesson'] =Lesson::with('contents')->where('id',$lesson->id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course or Lesson with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function lessonContentEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>'required',
            'body_ru'=>'required',
            'body_uz'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $content = LessonContent::find($data['id']);
      
        if($content){
            try {
                $res =DB::transaction(function () use ($content, $data){
                    $content->updateModel($data);
                    $responseArr['lessonContent'] =$content;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course or Lesson with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function lessonContentDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $lessonContent = LessonContent::find($data['id']);
        if($lessonContent){
            try {
               
                $res =DB::transaction(function () use ($lessonContent, $data){
                    $lessonContent->deleteModel($data);
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('LessonContent with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function lessonDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $lesson = Lesson::find($data['lesson_id']);
        
        if($lesson){
            if($lesson->quizes){
                return ErrorHelperResponse::returnError('First Delete Quizz of the Lesson',Response::HTTP_FOUND);
            }
            try {
                $res =DB::transaction(function () use ($lesson, $data){
                    $lesson->deleteModel($data);
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Lesson with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function lessonContentAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id'=>'required',
            'contents'=>'required|json',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        $lesson = Lesson::find($data['lesson_id']);
        if($lesson){
            try {
                $res =DB::transaction(function () use ($lesson, $data){
                    
                    $lesson->createContent($data['lesson_id'], $data);
                    $responseArr['lesson'] =Lesson::with('contents')->where('id',$lesson->id)->first();
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
    public function lessonContentImageAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id'=>'required',
            'type'=>'required|in:img',
            'body_ru'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            'body_uz'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            'contentOrder'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        $lesson = Lesson::find($data['lesson_id']);
        if($lesson){
            try {
                $res =DB::transaction(function () use ($lesson, $data){
                    $lessonContent = new LessonContent();
                    $lessonContent->saveImgModel($data['lesson_id'],$data);
                    $responseArr['lesson'] =Lesson::with('contents')->where('id',$lesson->id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course or Lesson with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function lessonContentImageEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>'required',
            'body_ru'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            'body_uz'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        $lessonContent = LessonContent::find($data['id']);
        if($lessonContent){
            try {
                $res =DB::transaction(function () use ($lessonContent, $data){
                   
                    $lessonContent->updateImgModel($data);
                    $responseArr['lesson'] =Lesson::with('contents')->where('id',$lessonContent->lesson_id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course or Lesson with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }

    public function logs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'lesson_id'=>'required',
            'typeContent'=>'required|min:0|max:1'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid', $data['user_id'])->first();
        if(!$user){
            return ErrorHelperResponse::returnError('User with given id not found',Response::HTTP_NOT_FOUND);
        }
        $lesson = Lesson::find($data['lesson_id'])->first();
        if(!$lesson){
            return ErrorHelperResponse::returnError('Lesson with given id not found',Response::HTTP_NOT_FOUND);
        }
        $lessonLog = LessonLog::where('lesson_id',$data['lesson_id'])->where('user_id',$user->id)->first();
        if(!$lessonLog){
            $lessonLog = new LessonLog();
        }
        try {
            $res = DB::transaction(function () use ($lessonLog, $user, $data){
                $lessonLog->saveOrUpdate($user->id, $data);
                $responseArr['lesson_log'] = $lessonLog;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    public function lessonExtraMaterialAdd(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'lesson_id'=>'required',
            'documentName'=>'required',
            'document'=>'required|mimes:pdf,doc,docx,txt,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $lesson = Lesson::find($data['lesson_id']);
        if($lesson){
            try {
                $res =DB::transaction(function () use ($data){
                    $lessonExtra = new LessonExtraMaterial();
                    $lessonExtra->saveModel($data,'created');
                    $responseArr['lessonExtra'] =$lessonExtra;
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
    public function lessonExtraMaterialEdit(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'extraMaterial_id'=>'required',
            'documentName'=>'required',
            'document'=>'mimes:pdf,doc,docx,txt,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $lessonExtraMaterial = LessonExtraMaterial::find($data['extraMaterial_id']);
        if($lessonExtraMaterial){
            try {
                $res =DB::transaction(function () use ($data, $lessonExtraMaterial){
                    $lessonExtraMaterial->updateModel($data,'updated');
                    $responseArr['lessonExtra'] =$lessonExtraMaterial;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('LessonExtraMaterial  with given id not found',Response::HTTP_NOT_FOUND);
        }
    }

    public function lessonExtraMaterialDelete(Request $request, $extra_id)
    {
        $lessonExtraMaterial = LessonExtraMaterial::find($extra_id);
        if(!$lessonExtraMaterial){
            return ErrorHelperResponse::returnError('LessonExtraMaterial with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($lessonExtraMaterial){
            try {
                $res = DB::transaction(function () use ($lessonExtraMaterial, $data){
                    $lessonExtraMaterial->deleteModel($data,'deleted');
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
