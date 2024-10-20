<?php

namespace App\Models;

use App\Models\Company\TeamAdress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTeams extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($data, $status)
    {
        if($status!='updated'){
            $this->company_id =$data['company_id'];
        }
        $this->teamName =$data['teamName']; 
        $this->teamType = $data['teamType'];
        $this->save();
        $companyTeamHistory = new CompanyTeamHistories();
        $companyTeamHistory->saveModel($this, $data, $status);
    }
    public function deleteModel($data, $status)
    {
        $companyTeamHistory = new CompanyTeamHistories();
        $companyTeamHistory->saveModel($this, $data, $status);
        $this->delete();
    }
    public function companyTeamAddress()
    {
        return $this->hasOne(TeamAdress::class, 'team_id');
    }
    public function companyTeamList()
    {
        return $this->hasMany(CompanyTeamLists::class, 'team_id');
    }
}
