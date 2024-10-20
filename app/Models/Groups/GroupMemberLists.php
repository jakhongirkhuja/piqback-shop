<?php

namespace App\Models\Groups;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMemberLists extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
   
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function saveModel($group_id, $user_id, $data)
    {
        $this->group_id = $group_id;
        $this->memberID = $user_id;
        $this->save();
        $groupMembersHistory = new GroupMemberListHistories();
        $groupMembersHistory->saveModel($this, $data);
    }
    public function deleteModel($data)
    {
        $groupMembersHistory = new GroupMemberListHistories();
        $groupMembersHistory->deleteModel($this, $data);
        $this->delete();
    }
}
