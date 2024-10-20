<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scout extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($group_id, $user_id, $data)
    {
       
       $this->group_id = $group_id;
       $this->scout_id  = $user_id;
       $this->save();
    
       $scoutHistory = new ScoutHistory();
      
       $scoutHistory->saveModel($this, $data);
       
    }
}
