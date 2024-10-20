<?php

namespace App\Models\Groups;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetFilterHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data, $status)
    {
        $this->group_id= $model->group_id;
        $this->ageRange= $model->ageRange;
        $this->roleList =$model->roleList;
        $this->gender =$model->gender;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('target_filter_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');
    }
}
