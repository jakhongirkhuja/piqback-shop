<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBioHistoires extends Model
{
    use HasFactory;
    protected $guarded= [];

    public function saveModel($model, $data)
    {
        $this->user_id = $model->id;
        $this->firstName= $model->firstName;
        $this->lastName= $model->lastName;
        $this->gender= $model->gender;
        $this->birthDate= $model->birthDate;
        $this->role= $model->role;
        $this->hrid = $model->hrid;
        $this->save();
        StandardAttributes::setSA('user_bio_histoires',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
    }
    public function updateModel($model, $data)
    {
        $this->user_id = $model->id;
        $this->firstName= $model->firstName;
        $this->lastName= $model->lastName;
        $this->gender= $model->gender;
        $this->birthDate= $model->birthDate;
        $this->role= $model->role;
        $this->hrid = $model->hrid;
        $this->save();
        StandardAttributes::setSA('user_bio_histoires',$this->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
    }
}
