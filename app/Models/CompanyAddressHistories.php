<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAddressHistories extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data)
    {
        $this->country_id = 1;
        $this->city_id = $model->city_id;
        $this->region_id = $model->region_id;
        $this->addressType = $model->addressType;
        $this->company_id = $model->company_id;
        $this->addressline1 =$model->addressline1;
        $this->longitude = $model->longitude;
        $this->latitude = $model->latitude;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('company_address_histories',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');
    }
    public function updateModel($model, $data)
    {
        $this->country_id = 1;
        $this->city_id = $model->city_id;
        $this->region_id = $model->region_id;
        $this->addressType = $model->addressType;
        $this->company_id = $model->company_id;
        $this->addressline1 =$model->addressline1;
        $this->longitude = $model->longitude;
        $this->latitude = $model->latitude;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('company_address_histories',$this->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');
    }
}
