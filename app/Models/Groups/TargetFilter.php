<?php

namespace App\Models\Groups;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetFilter extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
   
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function saveModel($data, $status)
    {
        $this->group_id= $data['group_id'];
        $this->ageRange= $data['ageRange'];
        $this->roleList = json_encode(explode(",",$data['roleList']));
        if($data['gender']!='all'){
            $this->gender= $data['gender'];
        }
        $this->save();
        $phonebookhistory = new TargetFilterHistory();
        $phonebookhistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data)
    {
        $promoceCodehistory = new TargetFilterHistory();
        $promoceCodehistory->saveModel($this,$data,'deleted');
        $this->delete();
    }
}
