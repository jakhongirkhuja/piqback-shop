<?php

namespace App\Models\Quizzes;

use App\Models\Lessons\Lesson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizz extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $hidden = [
        'created_at',
        'updated_at',
       
    ];
    protected $cast = [
        'lesson_id'=>'integer',
    ];
    /**
     * Get the user associated with the Quizz
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quizlog()
    {
        if(auth()->user()){
            return $this->hasMany(QuizLog::class, 'quiz_id')->where('user_id', auth()->user()->id);
        }else{
            return $this->hasMany(QuizLog::class, 'quiz_id');
        }
        
    }
    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id')->orderby('created_at','desc');
    }
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
   
    public function quizLimit()
    {
        return $this->hasOne(QuizLimit::class);
    }
    public function saveModel($lesson_id,$quizzes, $data )
    {
        
        $questions = isset(json_decode($data['quiz'],true)['questions'])? json_decode($data['quiz'],true)['questions'] : false;
        $this->lesson_id = $lesson_id;
        $this->prizeIQC = $quizzes['quizz']['prizeIQC'];
        $this->timeLimits = $quizzes['quizz']['timeLimits'];
        $this->numberRightAnswersToPass = $quizzes['quizz']['numberRightAnswersToPass'];
        $this->type = $data['type'];
        if($data['type']=='with limited reward'){
            $this->prizeLimit = $data['prizeLimit'];
        }
        $this->prizeStatus = $data['prizeStatus'];
        $this->save();
        $quizzHistory = new QuizzHistory();
        $quizzHistory->saveModel($this, $data,'created');
        if($questions){
            foreach ($questions as $key => $question) {
                $question_each['ru'] = $question['question']['question_ru'];
                $question_each['uz'] =$question['question']['question_uz'];
                
                $questionTextOne['ru'] = $question['question']['questionTextOne_ru'];
                $questionTextOne['uz'] =$question['question']['questionTextOne_uz'];
    
                $questionTextTwo['ru'] = $question['question']['questionTextTwo_ru'];
                $questionTextTwo['uz'] =$question['question']['questionTextTwo_uz'];
                $questionType = $question['question']['questionType'];
                $questionIMG = isset($data['questionIMG'])? $data['questionIMG'] : false;
                /// save question each function call 
                $newquestion = new Question();
                $question_id = $newquestion->saveModel($this->id,$question_each,$questionTextOne, $questionTextTwo,$questionType,$questionIMG,$data);
                // then answers to each 
                $variants = $question['variants'];
                $variantRightOne = true;
                // dd($variants);
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
                    $variantsave->saveModel($variant_each, $right_answer,$question_id, $variant_keywords,$data );
                    
                }
            }
        }

    }
    public function editModel($data)
    {
        $this->lesson_id = $data['lesson_id'];
        $this->prizeIQC = $data['prizeIQC'];
        $this->timeLimits = $data['timeLimits'];
        $this->numberRightAnswersToPass = $data['numberRightAnswersToPass'];
        $this->type = $data['type'];
        if($data['type']=='with limited reward'){
            $this->prizeLimit = $data['prizeLimit'];
        }
        $this->prizeStatus = $data['prizeStatus'];
        $this->save();
        $quizzHistory = new QuizzHistory();
        $quizzHistory->saveModel($this, $data, 'updated');
    }
    public function deleteModel($data)
    {
        $quizzHistory = new QuizzHistory();
        $quizzHistory->saveModel($this, $data, 'deactivated');
        $this->delete();
    }
}
