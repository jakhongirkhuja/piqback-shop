<?php

namespace App\Models\Lessons;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonExtraMaterialHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($model, $data, $status)
    {
        $this->lesson_id = $model->lesson_id;
        $this->documentName = $model->documentName;
        $this->documentURL =$model->documentURL;
        $this->documentSize= $model->documentSize;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('lesson_extra_material_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
    }
}
