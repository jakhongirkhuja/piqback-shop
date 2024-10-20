<?php

namespace App\Models\Lessons;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonContentHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data)
    {
        $this->lesson_id = $model->id;
        $this->type = $model->type;
        $this->contentOrder = $model->contentOrder;
        $this->body = $model->body;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('lesson_content_histories',$this->id,'created',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
        
    }
    public function updateModel($model, $data)
    {
        $this->lesson_id = $model->id;
        $this->type = $model->type;
        $this->contentOrder = $model->contentOrder;
        $this->body = $model->body;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->save();
        StandardAttributes::setSA('lesson_content_histories',$this->id,'updated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
    }

    public function deleteModel($model, $data)
    {
        
        
            $this->lesson_id = $model->id;
            $this->type = $model->type;
            $this->contentOrder = $model->contentOrder;
            $this->body = $model->body;
            $user_id = auth()->user()? auth()->user()->id : 1;
            $this->moderated = $user_id;
            $this->save();
            StandardAttributes::setSA('lesson_content_histories',$this->id,'deactivated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
        
    }
}
