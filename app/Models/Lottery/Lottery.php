<?php

namespace App\Models\Lottery;

use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    use HasFactory;
    protected $connection = 'pgsql10';
    protected $appends = ['course'];
    public function getCourseAttribute()
    {
        return Course::with('getinfo')->find($this->course_id); 
    }
    public function saveModel($data, $status){
        $this->course_id = $data['course_id'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->limit = $data['limit'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->deadline = $data['deadline'];
        $this->save();
        $lessonHistory  = new LotteryHistory();
        $lessonHistory->saveModel($this, $data, $status);
    }
    public function changeStatus($data,$status){
        $lessonHistory  = new LotteryHistory();
        $lessonHistory->saveModel($this, $data, $status);
    } 
    public function deleteModel($data, $status){
        $lessonHistory  = new LotteryHistory();
        $lessonHistory->saveModel($this, $data, $status);
        $this->delete();
    }
    
    public function lotteryLogs()
    {
        return $this->hasMany(LotteryLog::class);
    }
    public function lotteryUserLogs()
    {
        return $this->hasMany(LotteryLog::class);
    }
    
}
