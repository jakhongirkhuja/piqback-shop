<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailHistories extends Model
{
    use HasFactory;
    public function saveModel($model, $data)
    {
        $this->user_id = $model->user_id;
        $this->email = $data['email'];
        if($this->save()){
            StandardAttributes::setSA('email_histories',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
        }
    }
    public function updateModel($model, $data)
    {
        $this->user_id = $model->user_id;
        $this->email = $model->email;
        if($this->save()){
            StandardAttributes::setSA('email_histories',$this->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
        }
    }
}
