<?php

namespace App\Models\Groups;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRestrictionList extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    /**
     * Get the user that owns the CompanyRestrictionList
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
        
    }
    public function saveModel($data, $status)
    {
        $this->group_id= $data['group_id'];
        $this->company_id = $data['company_id'];
        $this->save();
        $companyRestrictionListHistory = new CompanyRestrictionListHistory();
        $companyRestrictionListHistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data)
    {
        $companyRestrictionListHistory = new CompanyRestrictionListHistory();
        $companyRestrictionListHistory->saveModel($this,$data,1);
        $this->delete();
    }
}
