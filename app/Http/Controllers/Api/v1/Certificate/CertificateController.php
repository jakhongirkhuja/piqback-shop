<?php

namespace App\Http\Controllers\Api\v1\Certificate;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Certificate\Certificate;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
{
    public function certificateGet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id'=>'required|numeric',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $user_id = auth()->user()?auth()->user()->id : 1;
        $certificate = Certificate::with('courses.getinfo')->where('user_id',$user_id)->where('course_id', $request->course_id)->first();
        return response()->json($certificate, Response::HTTP_OK);
    }
    public function certificatePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id'=>'required|numeric',
            'expirationDate'=>'required|numeric',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user_id = auth()->user()? auth()->user()->id : 1;
        $certificate = Certificate::where('user_id',$user_id)->where('course_id', $data['course_id'])->first();
        if(!$certificate){
            $course  = Course::find($data['course_id']);
            if($course){
                
                try {
                    $res =DB::transaction(function () use ($data){
                        $certificate = new Certificate();
                        $certificate->course_id= $data['course_id'];
                        $certificate->user_id= auth()->user()? auth()->user()->id : 1;
                        if($data['expirationDate']!=0){
                            $certificate->expirationDate = $data['expirationDate'];
                        }
                        $certificate->save();
                        
                        $responseArr['certificate_id'] =$certificate->id;
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
        }else{
            return ErrorHelperResponse::returnError('Certificate is promoted to the user by given course id',Response::HTTP_FOUND);
        } 
        
    }
}
