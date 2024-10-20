<?php

namespace App\Models\Company;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamAddressHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data, $status)
    {
        $this->country_id = 1;
        $this->city_id = $model->city_id;
        $this->region_id = $model->region_id;
        $this->addressType = $model->addressType;
        $this->team_id = $model->team_id;
        $this->addressline =$model->addressline;
        $this->longitude = $model->longitude;
        $this->latitude = $model->latitude;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('team_address_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');
    }
    
}
