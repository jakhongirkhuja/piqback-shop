<?php

namespace App\Models\Lottery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryHistory extends Model
{

    use HasFactory;
    protected $connection = 'pgsql10';
    public function saveModel($model, $data ,$status){
        $this->lottery_id = $model->id;
        $this->course_id = $model->course_id;
        $this->startDate = $model->startDate;
        $this->endDate = $model->endDate;
        $this->limit = $model->limit;
        $this->name = $model->name;
        $this->description = $model->description;
        $this->deadline = $model->deadline;
        $this->editor = auth()->user()?auth()->user()->id : 1;
        $this->status = $status;
        $this->save();
    }
}
