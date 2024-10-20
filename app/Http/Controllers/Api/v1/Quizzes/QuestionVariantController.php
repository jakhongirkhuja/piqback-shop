<?php

namespace App\Http\Controllers\Api\v1\Quizzes;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Quizzes\Question;
use App\Models\Quizzes\QuestionVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionVariantController extends Controller
{
    
    public function variantadd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id'=>'required',
            'variant'=>'required|json',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }

        $data = $request->all();
        $question  = Question::with('variants')->find($data['question_id']);
        if($question){
            try {
                $res =DB::transaction(function () use ($data,$question){
                    $variant = json_decode($data['variant'],true);
                   
                    $variant_each['ru'] = $variant['variant']['variantText_ru'];
                    $variant_each['uz'] = $variant['variant']['variantText_uz'];
                    $variant_keywords['ru'] = $variant['variant']['variantKeywords_ru'];
                    $variant_keywords['uz'] = $variant['variant']['variantKeywords_uz'];
                    $right_answer = $variant['variant']['rightAnswer'];
                    if($question->questionType=='filltheblank' || $question->questionType=='gapfill'){
                        $variants = $question->variants;
                        foreach ($variants as $key => $variant) {
                            $variant->deleteModel($data);
                        }
                    }
                    $variantsave = new QuestionVariant();
                    
                    $variantsave->saveModel($variant_each, $right_answer,$data['question_id'], $variant_keywords,$data );
                   
                    $variantsave->rightAnswer = filter_var($variantsave->rightAnswer, FILTER_VALIDATE_BOOLEAN);
                    $variantsave->question_id = filter_var($variantsave->question_id, FILTER_VALIDATE_INT);
                    $responseArr['variant'] =$variantsave;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Question  with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function variantedit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id'=>'required',
            'variant_id'=>'required',
            'variantText'=>'required',
            'rightAnswer'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }

        $data = $request->all();
        $question  = Question::find($data['question_id']);
        $variant = QuestionVariant::find($data['variant_id']);
        if($question && $variant){
            try {
                if($question->questionType=='filltheblank' || $question->questionType=='gapfill'){
                    $variantsGets = $question->variants;
                    foreach ($variantsGets as $key => $variantsGet) {
                        if($variantsGet->id !=$data['variant_id'] ){
                            $variantsGet->deleteModel($data);
                        }
                       
                    }
                }
                $res =DB::transaction(function () use ($variant,$data){
                   
                    $variant->editModel($data);
                    $variant->rightAnswer = filter_var($variant->rightAnswer, FILTER_VALIDATE_BOOLEAN);
                    $variant->question_id = filter_var($variant->question_id, FILTER_VALIDATE_INT);
                    $responseArr['variant'] =$variant;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Question or Question Variant with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function variantDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }

        $data = $request->all();
        $variant  = QuestionVariant::find($data['variant_id']);
        if($variant){
            try {
                $res =DB::transaction(function () use ($variant,$data){
                    $variant->deleteModel($data);
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Question variant with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    
}
