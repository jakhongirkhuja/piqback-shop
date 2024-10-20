<?php

namespace App\Models\Quizzes;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizLog extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveOrUpdate($user_id, $data)
    {
        $this->user_id = $user_id;
        $this->quiz_id = $data['quiz_id'];
        $this->quizAttempt = $data['quizAttempt'];
        $this->numberOfRightAnswers = $data['numberOfRightAnswers'];
        $this->timeLeft = $data['timeLeft'];
        $this->addressIP  = request()->ip();
        $this->platform = $data['platform'];
        $this->device = $data['device'];
        $this->browser = $data['browser'];
        $this->timeZone = $data['timeZone'];
        $this->save();
    }
}
