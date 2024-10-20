<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoutedList extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($group_id,$data)
    {
        $this->group_id = $group_id;
        $this->scout_id = $data['scout_id'];
        $this->company_id = $data['company_id'];
        $this->save();
        $scoutedHistory = new ScoutedListHistory();
        $scoutedHistory->saveModel($this, $data);
        
    }
}
