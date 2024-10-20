<?php

namespace App\Models\Groups;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRestrictionListHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data, $status)
    {
        $this->company_id = $model->company_id;
        $this->group_id = $model->group_id;
        $this->moderated=  auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('company_restriction_list_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql2');
    }
}
