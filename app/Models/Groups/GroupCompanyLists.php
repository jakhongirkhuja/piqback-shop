<?php

namespace App\Models\Groups;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCompanyLists extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    
    public function company()
    {
        return $this->hasOne(Company::class,'id' , 'company_id');
    }
}
