<?php

namespace App\Models\Quizzes;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data, $status)
    {
        $this->quiz_id  =$model->quiz_id;
        $this->question_id = $model->id;
        $this->question = $model->question;
        $this->questionType = isset($data['questionType']) && $data['questionType']!=''? $data['questionType'] : 'single'; 
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('question_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
        return $this->id;
    }
}
