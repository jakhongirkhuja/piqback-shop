<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyTeamLists extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($data, $status)
    {
        if($status==0){
            $this->team_id = $data['team_id'];
            $this->teamMember = $data['user_id'];
            $this->save();
        }
        $companyTeamHistory = new CompanyTeamListHistories();
        $companyTeamHistory->saveModel($this, $data, $status);
        if($status==1){
            $this->delete();
        }
    }
    
    public function companyTeam()
    {
        return $this->belongsTo(CompanyTeams::class, 'team_id');
    }
}
