<?php

namespace App\Models\Lessons;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data)
    {
        $this->course_id = $model->course_id;
        $this->lesson_id = $model->id;
        $this->order = $model->order;
        $this->lessonTitleName = $model->lessonTitleName;
        $this->video = $model->video;
        $this->videoLength = $model->videoLength;
        $this->videoLocId =  $model->videoLocId;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('lesson_histories',$this->id,'created',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
    }
    public function updateModel($model, $data)
    {
        $this->course_id = $model->course_id;
        $this->lesson_id = $model->id;
        $this->order = $model->order;
        $this->lessonTitleName = $model->lessonTitleName;
        $this->video = $model->video;
        $this->videoLength = $model->videoLength;
        $this->videoLocId =  $model->videoLocId;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('lesson_histories',$this->id,'updated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
    }
    public function deleteModel($model, $data)
    {
        $this->course_id = $model->course_id;
        $this->lesson_id = $model->id;
        $this->order = $model->order;
        $this->lessonTitleName = $model->lessonTitleName;
        $this->video = $model->video;
        $this->videoLocId =  $model->videoLocId;
        $this->videoLength = $model->videoLength;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('lesson_histories',$this->id,'deactivated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
    }
}
