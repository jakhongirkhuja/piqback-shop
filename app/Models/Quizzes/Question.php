<?php

namespace App\Models\Quizzes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Question extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $cast = [
        'quiz_id'=>'integer'
    ];
    public function variants()
    {
        return $this->hasMany(QuestionVariant::class, 'question_id');
    }
    public function saveModel($quiz_id, $question, $questionTextOne, $questionTextTwo, $questionType,$questionIMG, $data)
    {
        $this->quiz_id  =$quiz_id;
        $this->question = json_encode($question);
        $this->questionTextOne = json_encode($questionTextOne);
        $this->questionTextTwo = json_encode($questionTextTwo);
        $this->questionType =$questionType;
        if($questionIMG ){
            $questionIMPath = (string) Str::uuid().'-'.Str::random(15).'.'.$questionIMG->getClientOriginalExtension();
            $questionIMG->move(public_path('/files/questions'),$questionIMPath);
            $this->questionIMG = $questionIMPath;
        } 
        $this->save();
        $questionHistory = new QuestionHistory();
        $questionHistory->saveModel($this, $data, 'created');
        return $this->id;
    }
    public function editModel($data)
    {
        $this->questionType = isset($data['questionType']) && $data['questionType']!=''? $data['questionType'] : 'single'; 
        $this->quiz_id = $data['quizz_id'];
        $questionTextOne = json_decode($this->questionTextOne);
        if(isset( $questionTextOne->ru)){
            $questionTextOneTest['ru'] = $questionTextOne->ru;
        }
        if(isset( $questionTextOne->uz)){
            $questionTextOneTest['uz'] = $questionTextOne->uz;
        }
        if(isset($data['questionTextOne']) && $data['questionTextOne']!='null' && $data['questionTextOne']){
            if($data['language']=='uz'){
                $questionTextOneTest['uz'] = $data['questionTextOne'];
            }else{
                $questionTextOneTest['ru'] = $data['questionTextOne'];
            }
            $this->questionTextOne = json_encode($questionTextOneTest);
        }
        

        $questionTextTwo = json_decode($this->questionTextTwo);
        if(isset( $questionTextTwo->ru)){
            $questionTextTwoTest['ru'] = $questionTextTwo->ru;
        }
        if(isset( $questionTextOne->uz)){
            $questionTextTwoTest['uz'] = $questionTextTwo->uz;
        }
        if(isset($data['questionTextTwo']) && $data['questionTextTwo']!='null' && $data['questionTextTwo']){
            if($data['language']=='uz'){
                $questionTextTwoTest['uz'] = $data['questionTextTwo'];
            }else{
                $questionTextTwoTest['ru'] = $data['questionTextTwo'];
            }
            $this->questionTextTwo = json_encode($questionTextTwoTest);
        }
        


        $question  = json_decode($this->question);
        if(isset( $question->ru)){
            $test['ru'] = $question->ru;
        }
        if(isset( $question->uz)){
            $test['uz'] = $question->uz;
        }
        if($data['language']=='uz'){
            $test['uz'] = $data['question'];
        }else{
            $test['ru'] = $data['question'];
        }
        $this->question = json_encode($test);
        $this->save();
        $questionHistory = new QuestionHistory();
        $questionHistory->saveModel($this, $data, 'updated');
    }
    public function editModelNew($data)
    {
        $question = json_decode($data['question'],true);  
        $question_each['ru'] = $question['question']['question_ru'];
        $question_each['uz'] =$question['question']['question_uz'];
        
        $questionTextOne['ru'] = $question['question']['questionTextOne_ru'];
        $questionTextOne['uz'] =$question['question']['questionTextOne_uz'];

        $questionTextTwo['ru'] = $question['question']['questionTextTwo_ru'];
        $questionTextTwo['uz'] =$question['question']['questionTextTwo_uz'];
        $questionType = $question['question']['questionType'];
        $questionIMG = isset($data['questionIMG'])? $data['questionIMG'] : false;
        if($questionIMG){
            if($this->questionIMG!=null && file_exists(public_path('/files/questions/'.$this->questionIMG))){
                unlink(public_path('/files/questions/'.$this->questionIMG));
            }
        } 
        $this->saveModel($this->quiz_id, $question_each,$questionTextOne, $questionTextTwo,$questionType,$questionIMG, $data);
        $variants = $question['variants'];
        $variantRightOne = true;
        foreach ($variants as $key => $variant) {
            $variant_each['ru'] = $variant['variantText_ru'];
            $variant_each['uz'] = $variant['variantText_uz'];
            $variant_keywords['ru'] = $variant['variantKeywords_ru'];
            $variant_keywords['uz'] = $variant['variantKeywords_uz'];
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
            $variantsave->saveModel($variant_each, $right_answer,$this->id, $variant_keywords, $data );
        }
    }
    public function deleteModel($data)
    {
        $questionHistory = new QuestionHistory();
        $questionHistory->saveModel($this, $data, 'deactivated');
        if($this->questionIMG!=null && file_exists(public_path('/files/questions/'.$this->questionIMG))){
            unlink(public_path('/files/questions/'.$this->questionIMG));
        }
        $this->delete();
    }
}
