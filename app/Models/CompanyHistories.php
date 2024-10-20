<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data, $status)
    {
        $this->company_id = $model->id;
        $this->user_id = $model->user_id;
        $this->companyName = $model->companyName;
        $this->moderated=  auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('company_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql2');
    }
}
