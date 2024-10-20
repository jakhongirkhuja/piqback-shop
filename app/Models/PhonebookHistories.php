<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhonebookHistories extends Model
{
    use HasFactory;
    public function saveModel($model, $data, $status)
    {
        $this->user_id = $model->user_id;
        $this->phoneNumber = $model->phoneNumber;
        $this->save();
        StandardAttributes::setSA('phonebook_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
    }
}
