<?php

namespace App\Models\Groups;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMemberListHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data)
    {
        $this->group_id = $model->group_id;
        $this->memberID = $model->memberID;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('group_member_list_histories',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');  
    }
    public function deleteModel($model, $data)
    {
        $this->group_id = $model->group_id;
        $this->memberID = $model->memberID;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('group_member_list_histories',$this->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');  
    }
}
