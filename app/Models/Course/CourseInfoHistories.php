<?php

namespace App\Models\Course;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseInfoHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';

    public function saveModel($course_id, $data, $savedInfo)
    {
        $this->course_id = $course_id;
        $this->courseTitleName = $savedInfo->courseTitleName;
        $this->courseBanner = $savedInfo->courseBanner;
        $this->courseInfo =  $savedInfo->courseInfo;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('course_info_histories',$this->id,'created',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
        return $this;
    }
    public function updateModel($course_id, $data, $savedInfo)
    {
        $this->course_id = $course_id;
        $this->courseTitleName = $savedInfo->courseTitleName;
        $this->courseBanner = $savedInfo->courseBanner;
        $this->courseInfo =  $savedInfo->courseInfo;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('course_info_histories',$this->id,'updated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
        return $this;
    }
    public function deleteModel($data, $savedInfo)
    {
        
        $this->course_id = $savedInfo->id;
        $this->courseTitleName = $savedInfo->courseTitleName;
        $this->courseBanner = $savedInfo->courseBanner;
        $this->courseInfo =  $savedInfo->courseInfo;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('course_info_histories',$this->id,'archived',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
        return $this;
    }
}
