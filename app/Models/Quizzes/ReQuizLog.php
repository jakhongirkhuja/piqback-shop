<?php

namespace App\Models\Quizzes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReQuizLog extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data)
    {
        $this->user_id = $model->user_id;
        $this->quiz_id = $model->quiz_id;
        $this->numberOfRightAnswers = $model->numberOfRightAnswers;
        $this->timeLeft =  $model->timeLeft;
        $this->addressIP  = request()->ip();
        $this->platform = $data['platform'];
        $this->device = $data['device'];
        $this->browser = $data['browser'];
        $this->timeZone = $data['timeZone'];
        $this->save();
        
    }
}
