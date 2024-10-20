<?php

namespace App\Models\Groups;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRestrictionList extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($data, $status)
    {
        $this->group_id= $data['group_id'];
        $this->memberID = $data['memberID'];
        $this->memberPhone = $data['memberPhone'];
        $this->save();
        $memberRestrictionListHistory = new MemberRestrictionListHistory();
        $memberRestrictionListHistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data)
    {
        $memberRestrictionListHistory = new MemberRestrictionListHistory();
        $memberRestrictionListHistory->saveModel($this,$data,1);
        $this->delete();
    }
}
