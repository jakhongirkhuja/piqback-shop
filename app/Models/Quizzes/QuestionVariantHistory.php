<?php

namespace App\Models\Quizzes;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionVariantHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data, $status)
    {
        $this->question_id  =$model->question_id;
        $this->variant_id  =$model->id;
        $this->variantText = $model->variantText;
        $this->rightAnswer = $model->rightAnswer;
        $this->keyWords =$model->keyWords;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('question_variant_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
        
    }
}
