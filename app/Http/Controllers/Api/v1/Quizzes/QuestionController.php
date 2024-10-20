<?php

namespace App\Http\Controllers\Api\v1\Quizzes;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Quizzes\Question;
use App\Models\Quizzes\QuestionVariant;
use App\Models\Quizzes\Quizz;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function questionadd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quizz_id'=>'required',
            'question'=>'required|json',
           
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        
        $quizz = Quizz::find($data['quizz_id']);
        
        if($quizz){
            try {
                $res =DB::transaction(function () use ($quizz,$data){
                    $question = json_decode($data['question'],true);  
                    $question_each['ru'] = $question['question']['question_ru'];
                    $question_each['uz'] =$question['question']['question_uz'];
                    
                    $questionTextOne['ru'] = $question['question']['questionTextOne_ru'];
                    $questionTextOne['uz'] =$question['question']['questionTextOne_uz'];

                    $questionTextTwo['ru'] = $question['question']['questionTextTwo_ru'];
                    $questionTextTwo['uz'] =$question['question']['questionTextTwo_uz'];
                    $questionType = $question['question']['questionType'];
                    $questionIMG = isset($data['questionIMG'])? $data['questionIMG'] : false;
                    
                    $questionNew = new Question();
                    $questionNew->saveModel($data['quizz_id'], $question_each,$questionTextOne, $questionTextTwo, $questionType, $questionIMG, $data);
                    $variants = $question['variants'];
                    $variantRightOne = true;
                    foreach ($variants as $key => $variant) {
                        $variant_each['ru'] = $variant['variantText_ru'];
                        $variant_each['uz'] = $variant['variantText_uz'];
                        $variant_keywords['ru'] = isset($variant['variantKeywords_ru'])? $variant['variantKeywords_ru'] : '';
                        $variant_keywords['uz'] = isset($variant['variantKeywords_uz'])? $variant['variantKeywords_uz'] : '';
                        if($questionType=='single'){
                            if($variantRightOne && $variant['rightAnswer']==true){
                                $right_answer = $variant['rightAnswer'];
                                $variantRightOne = false;
                            }else{
                                $right_answer = false;
                            }
                        }else{
                            $right_answer = $variant['rightAnswer'];
                        }
                        
                        $variantsave = new QuestionVariant();
                        $variantsave->saveModel($variant_each, $right_answer,$questionNew->id,$variant_keywords, $data );
                    }
                    $responseArr['question'] =Question::with('variants')->where('id', $questionNew->id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Quiz  with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function questionedit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id'=>'required',
            'question'=>'required|json',
           
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $question  = Question::find($data['question_id']);
        
        if($question ){
            try {
                $res =DB::transaction(function () use ($question,$data){
                    $variants = $question->variants;
                   
                    $question->editModelNew($data);
                    if(count($variants)>0){
                        foreach ($variants as $key => $variant) {
                            $variant->delete();
                        }
                    }
                    $responseArr['question'] =$question;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Question with given ids not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function questionDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $question  = Question::find($data['question_id']);
        if($question){
            if($question->variants->count()==0){
                try {
                    $res =DB::transaction(function () use ($question,$data){
                        $question->deleteModel($data);
                        $responseArr['message'] = 'Deleted';
                        return response()->json($responseArr, Response::HTTP_OK);
                    });
                    return $res;
                } catch (\Throwable $th) {
                    return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }else{
                return ErrorHelperResponse::returnError('First delete variants of Question',Response::HTTP_FOUND);
            }
        }else{
            return ErrorHelperResponse::returnError('Question with given id not found',Response::HTTP_NOT_FOUND);
        }
    }
}
