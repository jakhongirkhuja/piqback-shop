<?php

namespace App\Models\Course;

use App\Helper\StandardAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';

    public function saveModel($data)
    {
        $this->category_id = $data['category_id'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->courseMonetized = $data['courseMonetized'];
        $this->coursePrice = $data['coursePrice'];
        $date = Carbon::parse($data['startDate']);
        $now = Carbon::now();
        $diff = $now->diffInDays($date,false);
        if($diff==0){
            $diffInhours = $now->diffInHours($date, false);
            if($diffInhours<=0){
                $this->courseType = 'ongoing';
            }else{
                $this->courseType = 'upcoming';
            }
        }elseif ($diff>0) {
            $this->courseType = 'upcoming';
        }elseif ($diff<0) {
            if($data['endDate']){
                $enddate = Carbon::parse($data['startDate']);
                $nowend = Carbon::now();
                $diffend = $nowend->diffInHours($date,false);
                if($diffend<=0){
                    $this->courseType = 'archived';
                }else{
                    $this->courseType = 'ongoing';
                }
            }else{
                $this->courseType = 'ongoing';
            }
            
        }
        $this->pin =$data['pin'];
        $this->order = $data['order'];
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('course_histories',$this->id,'created',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
        return $this;
    }
    public function saveModelNew($model,$data,$status)
    {
        if($data['category_id']=='special'){
            $this->courseTypeByAccess = 0;
            $this->courseForGroup = $data['courseForGroup'];
            $this->category_id = 1;
        }else{
            $this->category_id = $model->category_id;
        }
       
        $this->startDate = $model->startDate;
        $this->endDate = $model->endDate;
        $this->courseMonetized = $model->courseMonetized;
        $this->coursePrice = $model->coursePrice;
        $this->pin =  $model->pin;
        $this->order =  $model->order;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('course_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
        return $this;
    }
    public function updateModel($data)
    {
        $this->category_id = $data['category_id'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->courseMonetized = $data['courseMonetized'];
        $this->coursePrice = $data['coursePrice'];
        $date = Carbon::parse($data['startDate']);
        $now = Carbon::now();
        $diff = $now->diffInDays($date,false);
        if($diff==0){
            $diffInhours = $now->diffInHours($date, false);
            if($diffInhours<=0){
                $this->courseType = 'ongoing';
            }else{
                $this->courseType = 'upcoming';
            }
        }elseif ($diff>0) {
            $this->courseType = 'upcoming';
        }elseif ($diff<0) {
            if($data['endDate']){
                $enddate = Carbon::parse($data['startDate']);
                $nowend = Carbon::now();
                $diffend = $nowend->diffInHours($date,false);
                if($diffend<=0){
                    $this->courseType = 'archived';
                }else{
                    $this->courseType = 'ongoing';
                }
            }else{
                $this->courseType = 'ongoing';
            }
            
        }
        $this->pin =$data['pin'];
        $this->order = $data['order'];
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('course_histories',$this->id,'updated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
        return $this;
    }
    public function deleteModel($course, $data)
    {
        $this->category_id = $course->category_id;
        $this->startDate = $course->startDate;
        $this->endDate = $course->endDate;
        $this->courseMonetized = $course->courseMonetized;
        $this->coursePrice = $course->coursePrice;
        $this->courseType = $course->courseType;
        $this->pin =  $course->pin;
        $this->order =  $course->order;
        $this->moderated = auth()->user()?auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('course_histories',$this->id,'archived',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
        return $this;
    }
}
