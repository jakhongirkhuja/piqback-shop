<?php

namespace App\Models\Course;

use App\Models\Company;
use App\Models\Course\CoursePin;
use App\Models\Lessons\Lesson;
use App\Models\Money\IqcTransaction;
use App\Models\Quizzes\Quizz;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $casts= [
        'category_id'=>'integer',
        'startDate'=>'date:Y-m-d',
        'endDate'=>'date:Y-m-d',
        'coursePrice'=>'integer',
        'courseMonetized'=>'integer',
    ];
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    protected $appends = ['totalpass','usergot'];
    public function getTotalpassAttribute()
    {
        $courseInfo = CourseLog::where('course_id',$this->id)->count();
        return $courseInfo>0? $courseInfo-1: 0; 
    }
    public function getUserGotAttribute()
    {
        
        if(request()->route()->getName()=='admin_course_list'){
            if($this->lessons->count()>0){
                $iqc= 0;
                foreach($this->lessons as $lessons){
                    if($lessons->quizes){
                        $userGotTransactions = IqcTransaction::where('serviceName','quiz')->where('identityText', $lessons->quizes->id)->get();
                        if($userGotTransactions->count()>0){
                            
                            foreach ($userGotTransactions as $key => $userGotTransaction) {
                                $iqc += $userGotTransaction->amountofIQC;
                            }
                            
                        }
                    }
                    
                    
                }
                return $iqc;
            }
            
        }
        return 0;
        
        
        
        
        
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function getinfo()
    {
        return $this->hasOne(CourseInfos::class);
    }
    public function courselogAll()
    {
        return $this->hasMany(CourseLog::class);
    }
    public function courselog()
    {
        return $this->hasOne(CourseLog::class)->where('user_id', auth()->user()->id);
    }
    public function courselogIn()
    {
        $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
        $users = [];
        if($company && $company->companymembers->count()>0){
            $users = $company->companymembers->pluck('member_id');
        }
        return $this->hasMany(CourseLog::class)->whereIn('user_id', $users);
    }
    public function lessons(){
        return $this->hasMany(Lesson::class);
    }
    public function quizes(){
        return $this->hasMany(Quizz::class);
    }
    
    public function saveModel($data)
    {
        $this->category_id = $data['category_id'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->courseMonetized = $data['courseMonetized'];
        $this->coursePrice = $data['coursePrice'] == 0 ? null : $data['coursePrice'];
        
        
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
        if(isset($data['pin']) && ($data['pin']==true || $data['pin']==1)){
            if($data['order']==0){
                $courses = Course::where('pin', true)->orderby('order','desc')->get();
                
                if($courses->count()>=10){
                    $this->order = $courses[0]->order;
                    $courses[0]->order = null;
                    $courses[0]->pin = false;
                    $courses[0]->save();
                }else{
                    $this->order = isset($courses[0])? $courses[0]->order+1 : 1;
                }
            }else{
                $this->order = $data['order'];
                $course = Course::where('pin', true)->where('order', $data['order'])->first();
                if($course){
                    $course->order = null;
                    $course->pin = false;
                    $course->save();
                }
            }
            $this->pin = true;
        }else{
            $this->pin = false;
            $this->order = null;
        }
       
        $this->save();
        if($this->pin){
            $coursePinModify = CoursePin::where('pinOrder', $this->order)->first();
            if($coursePinModify){
                // $coursePinModify->saveModel($this->order, $this->id, 'created', $data);
            }else{
                $coursePinModify = new CoursePin();
                // $coursePinModify->saveModel($this->order, $this->id, 'created', $data);
            }
        }
        return $this;
    }
    public function saveModelAll($data, $accassPrivate)
    {
        if($data['category_id']=='special' || $accassPrivate){
            $this->courseTypeByAccess = 0;
            $this->access = 1;
            $this->courseForGroup = (int)$data['courseForGroup'];
            if($accassPrivate){
                if($data['category_id']=='special'){
                    $this->category_id = 1; 
                }else{
                    $this->category_id = $data['category_id'];
                }
            }else{
                $this->category_id = 1;
            }
            
        }else{
            if($data['category_id']=='special'){
                $this->category_id = 1; 
            }else{
                $this->category_id = $data['category_id'];
            }
            $this->access = 0;
        }
        
        $this->startDate = Carbon::parse(date("Y-m-d H:i:s",strtotime($data['startDate'])));
        $this->endDate =$data['endDate']!='null'? Carbon::parse(date("Y-m-d H:i:s",strtotime($data['endDate']))) : null;
        
        $this->courseMonetized = $data['courseMonetized'];
        $this->coursePrice =$data['courseMonetized']==0? null : ($data['coursePrice'] == 0 ? null : $data['coursePrice']);
        
        
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
        if(isset($data['pin']) && ($data['pin']=='true' || $data['pin']==1)){
            $this->pin = true;
            $this->order = null;
        }else{
            $this->pin = false;
            $this->order = null;
        }
        $this->save();
        
        $courseHistory = new CourseHistories();
        $courseHistory->saveModelNew($this,$data, 'created');
        $courseInfo = new CourseInfos();
        $courseInfo->saveModelNew($this->id,$data);
        $coursePins = CoursePin::where('course_id', $this->id)->get();
        if($coursePins->count()>0){
            foreach ($coursePins as $key => $coursePin) {
                $coursePin->deleteModel('deleted', $data);
            }
        }
        if($this->pin){
            $checkOrder = CoursePin::where('category_id', $this->category_id)->orderby('pinOrder', 'desc')->first();
            if($checkOrder){
                if($checkOrder->pinOrder<10){
                    $checkOrder->saveModel($this->category_id, $this->id, $accassPrivate, $checkOrder->pinOrder+1, 'created', $data);
                }else{
                    $checkOrder->saveModel($this->category_id, $this->id, $accassPrivate, 10, 'created', $data);
                }  
            }else{
                $coursePinModify = new CoursePin();
                $coursePinModify->saveModel($this->category_id, $this->id, $accassPrivate, 1 ,'created', $data);
            }
            
        }
        
    }
    public function updateModelNew($data, $accassPrivate)
    {
        if($data['category_id']=='special' || $accassPrivate){
            $this->courseTypeByAccess = 0;
            $this->access = 1;
            $this->courseForGroup = (int)$data['courseForGroup'];
            if($accassPrivate){
                if($data['category_id']=='special'){
                    $this->category_id = 1; 
                }else{
                    $this->category_id = $data['category_id'];
                }
                
            }else{
                $this->category_id = 1;
            }
            
        }else{
            $this->courseTypeByAccess = 1;
            if($data['category_id']=='special'){
                $this->category_id = 1; 
            }else{
                $this->category_id = $data['category_id'];
            }
            
            $this->access = 0;
        }

        
       
        $this->startDate = Carbon::parse(date("Y-m-d H:i:s",strtotime($data['startDate'])));
        $this->endDate =$data['endDate']!='null'? Carbon::parse(date("Y-m-d H:i:s",strtotime($data['endDate']))) : null;
        $this->courseMonetized = $data['courseMonetized'];
        $this->coursePrice =$data['courseMonetized']==0? null : ($data['coursePrice'] == 0 ? null : $data['coursePrice']);

        if(isset($data['pin']) && ($data['pin']=='true' || $data['pin']==1)){
            $this->pin = true;
            $this->order = null;
        }else{
            $this->pin = false;
            $this->order = null;
            
        }
        $coursePins = CoursePin::where('course_id', $this->id)->get();
        if($coursePins->count()>0){
            foreach ($coursePins as $key => $coursePin) {
                $coursePin->deleteModel('deleted', $data);
            }
        }
        
        $this->save();
        $courseHistory = new CourseHistories();
        $courseHistory->saveModelNew($this,$data,'updated');

        if($this->pin){
            $coursePinModify = CoursePin::where('category_id', $this->category_id)->where('course_id', $this->id)->first();
            if(!$coursePinModify){
                $checkOrder = CoursePin::where('category_id', $this->category_id)->orderby('pinOrder', 'desc')->first();
                if($checkOrder){
                    if($checkOrder->pinOrder<10){
                        $coursePinModify = new CoursePin();
                        $coursePinModify->saveModel($this->category_id, $this->id, $accassPrivate, $checkOrder->pinOrder+1, 'created', $data);
                    } 
                }else{
                    $coursePinModify = new CoursePin();
                    $coursePinModify->saveModel($this->category_id, $this->id, $accassPrivate, 1 ,'created', $data);
                }     
            }
        }
    }
    public function updateModel($data)
    {
        $this->category_id = $data['category_id'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->courseMonetized = $data['courseMonetized'];
        $this->coursePrice = $data['coursePrice'] == 0 ? null : $data['coursePrice'];
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
        $this->save();
        if(isset($data['pin']) && ($data['pin']==true || $data['pin']==1)){
            if($data['order']==0 && $this->order != $data['order']){
                $courses = Course::where('pin', true)->where('id','!=' ,$this->id)->orderby('order','desc')->get();
                
                if($courses->count()>=10){
                    $this->order = $courses[0]->order;
                    $courses[0]->order = null;
                    $courses[0]->pin = false;
                    $courses[0]->save();
                }else{
                    $this->order = isset($courses[0])? $courses[0]->order+1 : 1;
                }
            }
            $this->pin = true;
        }else{
            $this->pin = false;
            $this->order = null;
            $coursePin = CoursePin::where('course_id', $this->id)->first();
            if($coursePin){
                $coursePin->deleteModel('deleted', $data);
            }
        }

        if($this->pin){
            $coursePinModify = CoursePin::where('pinOrder', $this->order)->first();
            if($coursePinModify){
                $coursePinModify->saveModel($this->order, $this->id, 'created', $data);
            }else{
                $coursePinModify = new CoursePin();
                // $coursePinModify->saveModel($this->order, $this->id, 'created', $data);
            }
        }
        return $this;
    }
    public function infos()
    {
        return $this->hasOne(CourseInfos::class, 'course_id');
    }
    
}
