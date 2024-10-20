<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTeamListHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model,$data, $status)
    {
        $this->team_id = $model->team_id;
        $this->teamMember =$model->teamMember;
        $this->save();
        StandardAttributes::setSA('company_team_list_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');
    }
}
