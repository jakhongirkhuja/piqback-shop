<?php

namespace App\Models\Store;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreObjectHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->objectName = $model->objectName;
        $this->objectDescription = $model->objectDescription;
        $this->objectCost =$model->objectCost;
        $this->objectAmount= $model->objectAmount;
        $this->objectIMG = $model->objectIMG;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('store_object_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}
