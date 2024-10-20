<?php

namespace App\Models\Lessons;

use App\Models\Course\Course;
use App\Models\Quizzes\Quizz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function contents()
    {
        return $this->hasMany(LessonContent::class, 'lesson_id')->orderby('contentOrder','asc');
    }
    public function quizes()
    {
        return  $this->hasOne(Quizz::class);
    }
    public function materials()
    {
        return  $this->hasMany(LessonExtraMaterial::class);
    }
    public function lessonlog()
    {
        return  $this->hasOne(LessonLog::class)->where('user_id',auth()->user()->id);
        
    }
    public function specificlessonlog()
    {
        return  $this->hasMany(LessonLog::class);
        
    }
    public function saveModel($data)
    {
        $decoded = false;
        if(isset($data['contents']) && $data['contents']){
            $decoded = json_decode($data['contents']);
        }
        
        $this->course_id = $data['course_id'];
        $this->order = $data['order'];
        
        
        $test['uz'] = $data['lessonTitleName_uz'];
        $testv['uz'] = $data['video_uz'];
        $testvl['uz'] =  $data['videoLength_uz'];
        $test['ru'] = $data['lessonTitleName_ru'];
        $testv['ru'] =$data['video_ru'];
        $testvl['ru'] = $data['videoLength_ru'];
       
        $this->lessonTitleName = json_encode($test);
        $this->video = json_encode($testv);
        $this->videoLength = json_encode($testvl);
        
        if(isset($data['videoLocId_uz']) && isset($data['videoLocId_ru'])){
            $test['uz'] = $data['videoLocId_uz'];
            $test['ru'] = $data['videoLocId_ru'];
            $this->videoLocId = json_encode($test);
        }
        
        $this->save();

        $lessonHistory  = new LessonHistory();
        $lessonHistory->saveModel($this, $data);

        if($decoded){
            foreach ($decoded as $dec) {
                $lessonContent = new LessonContent();
                $lessonContent->saveModel($this->id, $data, $dec);
            }
            
        }
        
    }

    public function createContent($lesson_id, $data){
        $decoded = json_decode($data['contents']);
        foreach ($decoded as $dec) {
            $lessonContent = new LessonContent();
            $lessonContent->saveModel($lesson_id, $data, $dec);
        }
    }
    public function updateModel($data)
    {
        $this->course_id = $data['course_id'];
        $this->order = $data['order'];

        $lessonTitleName  = json_decode($this->lessonTitleName);
        if(isset( $lessonTitleName->ru)){
            $test['ru'] = $lessonTitleName->ru;
        }
        if(isset( $lessonTitleName->uz)){
            $test['uz'] = $lessonTitleName->uz;
        }

        $video  = json_decode($this->video);
        if(isset( $video->ru)){
            $testv['ru'] = $video->ru;
        }
        if(isset( $video->uz)){
            $testv['uz'] = $video->uz;
        }


        $videoLength  = json_decode($this->videoLength);
        if(isset( $videoLength->ru)){
            $testvl['ru'] = $videoLength->ru;
        }
        if(isset( $videoLength->uz)){
            $testvl['uz'] = $videoLength->uz;
        }

       
            $test['uz'] =$data['lessonTitleName_uz'];
            $testv['uz'] = $data['video_uz'];
            $testvl['uz'] = $data['videoLength_uz'];
            $test['ru'] = $data['lessonTitleName_ru'];
            $testv['ru'] =$data['video_ru'];
            $testvl['ru'] = $data['videoLength_ru'];
            
        
        $this->lessonTitleName = json_encode($test);
        $this->video = json_encode($testv);
        $this->videoLength = json_encode($testvl);

        if(isset($data['videoLocId_uz']) && isset($data['videoLocId_ru'])){
            $test['uz'] = $data['videoLocId_uz'];
            $test['ru'] = $data['videoLocId_ru'];
            $this->videoLocId = json_encode($test);
        }
        $this->save();
        $lessonHistory  = new LessonHistory();
        $lessonHistory->updateModel($this, $data);
        
        // $lessonContent = LessonContent::where('lesson_id', $this->id)->where('type',$data['type'])->first();
        // if($lessonContent){
        //     $lessonContent->updateModel($data);
        // }else{
        //     $lessonContent = new LessonContent();
        //     $lessonContent->saveModel($this->id, $data);
        // }
        // $lessonContent = LessonContent::where('lesson_id', $this->id)->first();
        
        // $lessonContent->updateModel($data);
    }

    public function deleteModel($data)
    {
        $lessonHistory  = new LessonHistory();
        
        $lessonHistory->deleteModel($this, $data);
        
        $lessonContentHistory = new LessonContentHistory();
        if($this->contents()->count()>0){
            foreach ($this->contents() as $key => $content) {
                $lessonContentHistory->deleteModel($content, $data);
                $this->delete();
            }
        }
        $this->delete();
    }
}
