<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseInfos extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    public function saveModel($course_id, $data)
    {
        
        $this->course_id = $course_id;
        $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['courseBanner']->getClientOriginalExtension();
        $data['courseBanner']->move(public_path('/files/course'),$bannerName);
        // Storage::putFileAs('/public/course/',$data['courseBanner'],$bannerName);
        $bannertest['ru'] = $bannerName;
        $this->courseBanner = json_encode($bannertest);

        $test['ru'] = $data['courseTitleName'];
        $this->courseTitleName = json_encode($test);

        $test2['ru'] = $data['courseInfo'];
        $this->courseInfo = json_encode($test2);


        $this->save();
        $courseInfoHistory = new CourseInfoHistories();
        $courseInfoHistory->saveModel($course_id,$data, $this);
        return $this;
    }
    public function saveModelNew($course_id, $data)
    {
        
        $this->course_id = $course_id;
        $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['courseBanner_ru']->getClientOriginalExtension();
        $data['courseBanner_ru']->move(public_path('/files/course'),$bannerName);
        // Storage::putFileAs('/public/course/',$data['courseBanner'],$bannerName);
        $bannertest['ru'] = $bannerName;

        $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['courseBanner_uz']->getClientOriginalExtension();
        $data['courseBanner_uz']->move(public_path('/files/course'),$bannerName);
        // Storage::putFileAs('/public/course/',$data['courseBanner'],$bannerName);
        $bannertest['uz'] = $bannerName;
        $this->courseBanner = json_encode($bannertest);

        $test['ru'] = $data['courseTitleName_ru'];
        $test['uz'] = $data['courseTitleName_uz'];
        $this->courseTitleName = json_encode($test);

        $test2['ru'] = $data['courseInfo_ru'];
        $test2['uz'] = $data['courseInfo_uz'];
        $this->courseInfo = json_encode($test2);


        $this->save();
        $courseInfoHistory = new CourseInfoHistories();
        $courseInfoHistory->saveModel($course_id,$data, $this);
    }
    public function updateModel($course_id, $data)
    {
        $this->course_id = $course_id;
        if((isset($data['courseBanner']))){
            $courseBanner = json_decode($this->courseBanner);
            if(isset( $courseBanner->ru)){
                $bannertest['ru'] = $courseBanner->ru;
            }
            if(isset( $courseBanner->uz)){
                $bannertest['uz'] = $courseBanner->uz;
            }
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['courseBanner']->getClientOriginalExtension();
            // Storage::putFileAs('/public/course/',$data['courseBanner'],$bannerName);
            $data['courseBanner']->move(public_path('/files/course'),$bannerName);
            if($data['language']=='uz'){
                $bannertest['uz'] = $bannerName;
            }else{
                $bannertest['ru'] = $bannerName;
            }
            $this->courseBanner = json_encode($bannertest);
        }
        



        $courseTitleName  = json_decode($this->courseTitleName);
        if(isset( $courseTitleName->ru)){
            $test['ru'] = $courseTitleName->ru;
        }
        if(isset( $courseTitleName->uz)){
            $test['uz'] = $courseTitleName->uz;
        }
        if($data['language']=='uz'){
            $test['uz'] = $data['courseTitleName'];;
        }else{
            $test['ru'] = $data['courseTitleName'];;
        }
        $this->courseTitleName = json_encode($test);




        $courseInfo  = json_decode($this->courseInfo);
        if(isset( $courseInfo->ru)){
            $test2['ru'] = $courseInfo->ru;
        }
        if(isset( $courseInfo->uz)){
            $test2['uz'] = $courseInfo->uz;
        }
        if($data['language']=='uz'){
            $test2['uz'] = $data['courseInfo'];;
        }else{
            $test2['ru'] = $data['courseInfo'];;
        }
        $this->courseInfo = json_encode($test2);



        $this->save();
        return $this;
    }
    public function updateModelNew($course_id, $data)
    {
        $this->course_id = $course_id;
        if((isset($data['courseBanner_ru']) && $data['courseBanner_ru']) || (isset($data['courseBanner_uz']) && $data['courseBanner_uz'])){
            $inside = false;
            $courseBanner = json_decode($this->courseBanner);
            if(isset( $courseBanner->ru)){
                $bannertest['ru'] = $courseBanner->ru;
                $inside =true;
            }
            if(isset( $courseBanner->uz)){
                $inside =true;
                $bannertest['uz'] = $courseBanner->uz;
            }
            if(isset($data['courseBanner_ru']) && $data['courseBanner_ru']){
                $inside =true;
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['courseBanner_ru']->getClientOriginalExtension();
                $data['courseBanner_ru']->move(public_path('/files/course'),$bannerName);
                $bannertest['ru'] = $bannerName;
            }
            if(isset($data['courseBanner_uz']) && $data['courseBanner_uz']){
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['courseBanner_uz']->getClientOriginalExtension();
                $data['courseBanner_uz']->move(public_path('/files/course'),$bannerName);
                $bannertest['uz'] = $bannerName;
                $inside =true;
            }
            if($inside){
                $this->courseBanner = json_encode($bannertest);
            }
        }
        $test['uz'] = $data['courseTitleName_uz'];
        $test['ru'] = $data['courseTitleName_ru'];
        $this->courseTitleName = json_encode($test);

        $test2['uz'] = $data['courseInfo_uz'];
        $test2['ru'] = $data['courseInfo_ru'];
        $this->courseInfo = json_encode($test2);
        $this->save();
        $courseInfoHistory = new CourseInfoHistories();
        $courseInfoHistory->updateModel($course_id,$data, $this);
    }
}
