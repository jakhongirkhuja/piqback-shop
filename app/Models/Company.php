<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($user_id, $data)
    {
        $this->companyName=  $data['companyName'];
        $this->user_id=  $user_id;
        $this->save();
        $companyHistory = new CompanyHistories();
        $companyHistory->saveModel($this, $data, 0);
    }
    public function updateOwnerModel($user_id, $data)
    {
        $this->user_id=  $user_id;
        $this->save();
        $companyHistory = new CompanyHistories();
        $companyHistory->saveModel($this, $data, 1);
    }
    public function updateCompanyNameModel($data)
    {
        $this->companyName=  strtoupper($data['companyName']);
        $this->save();
        $companyHistory = new CompanyHistories();
        $companyHistory->saveModel($this, $data, 1);
    }

    public function deleteModel($data)
    {
        $companyHistory = new CompanyHistories();
        $companyHistory->saveModel($this, $data, 3);
        $companyAddress = CompanyAddress::where('company_id', $this->id)->first();
        if($companyAddress){
            $companyAddress->deleteModel($data);
        }
        $this->delete();
    }
    public function companymembers()
    {
        return $this->hasMany(CompanyMembers::class);
    }
    public function companyadress()
    {
        return $this->hasOne(CompanyAddress::class);
    }
    public function teams()
    {
        return $this->hasMany(CompanyTeams::class);
    }
    
}
