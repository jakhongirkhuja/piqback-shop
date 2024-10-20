<?php

namespace App\Models\StoreLatest;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->category_id = $model->category_id;
        $this->storeOwner = $model->storeOwner;
        $this->storeName = $model->storeName;
        $this->storeLongitude = $model->storeLongitude;
        $this->storeLatitude = $model->storeLatitude;
        $this->storeLandmark = $model->storeLandmark;
        $this->storeDescription = $model->storeDescription;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('store_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}
