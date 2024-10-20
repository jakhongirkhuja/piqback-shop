<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyMemberHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data)
    {
        $this->company_id= $model->company_id;
        $this->member_id= $model->member_id;
        $this->moderated= auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('company_member_histories',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql2');
    }
    public function updateModel($model, $data)
    {
        $this->company_id= $model->company_id;
        $this->member_id= $model->member_id;
        $this->moderated= auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('company_member_histories',$this->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql2');
    }
    
}
