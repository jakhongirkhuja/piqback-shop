<?php

namespace App\Models\Groups;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRestrictionListHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data, $status)
    {
        $this->memberID = $model->memberID;
        $this->group_id = $model->group_id;
        $this->memberPhone = $model->memberPhone;
        $this->moderated=  auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('member_restriction_list_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql2');
    }
}
