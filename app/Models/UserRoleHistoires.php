<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoleHistoires extends Model
{
    use HasFactory;
    public function saveModel($model,$data, $status)
    {
        $this->user_id= $model->id;
        $this->role= $model->role;
        
        $this->save();
       
        StandardAttributes::setSA('user_role_histoires',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
    }
}
