<?php

namespace App\Models\Course;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePinHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data, $status){

        $this->access = $model->access;
        $this->category_id =  $model->category_id;
        $this->pinOrder = $model->id;
        $this->course_id = $model->course_id;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('course_pin_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
        
    }
}
