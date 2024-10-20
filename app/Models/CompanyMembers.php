<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyMembers extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function saveModel($user_id, $data)
    {
        $this->member_id = $user_id;
        $this->company_id = $data['company_id'];
        $this->save();
        $companyMembersHistory = new CompanyMemberHistories();
        $companyMembersHistory->saveModel($this, $data);
    }
    public function updateStatusModel($user_id, $data)
    {
        $this->member_id = $user_id;
        $this->company_id = $data['company_id'];
        $this->save();
        $companyMembersHistory = new CompanyMemberHistories();
        $companyMembersHistory->updateModel($this, $data);
    }
    public function updateMemberOnly($user_id, $data)
    {
        $this->member_id = $user_id;
        $this->save();
        $companyMembersHistory = new CompanyMemberHistories();
        $companyMembersHistory->updateModel($this, $data);
    }
    public function updateMemberStatus($data)
    {
        $this->memberStatus = $data['memberStatus'];
        $this->save();
        $companyMembersHistory = new CompanyMemberHistories();
        $companyMembersHistory->updateModel($this, $data);
    }
    public function deleteModel($data){
        $companyMembersHistory = new CompanyMemberHistories();
        $companyMembersHistory->updateModel($this, $data);
        $this->delete();
    }
}
