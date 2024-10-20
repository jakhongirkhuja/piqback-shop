<?php

namespace App\Models\Quizzes;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizzHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data, $status)
    {
        $this->quizz_id = $model->id;
        $this->lesson_id = $model->lesson_id;
        $this->prizeIQC = $model->prizeIQC;
        $this->timeLimits = $model->timeLimits;
        $this->numberRightAnswersToPass = $model->numberRightAnswersToPass;
        $this->type = $model->type;
        $this->prizeLimit = $model->prizeLimit;
        $this->prizeStatus = $model->prizeStatus;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('quizz_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
    }
    
}
