<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTeamHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model,$data, $status)
    {
        $this->company_id =$model->company_id;
        $this->teamName =$model->teamName;
         $this->save();
        StandardAttributes::setSA('company_team_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');
    }
}
