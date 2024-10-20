<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helper\StandardAttributes;
class PhysicalStoreHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->category_id = $model->category_id;
        $this->owner = $model->owner;
        $this->longitude =$model->longitude;
        $this->latitude= $model->latitude;
        $this->landmark = $model->landmark;
        $this->description = $model->description;
        $this->mainContact = $model->mainContact;
        $this->extraContact = $model->extraContact;
        $this->name = $model->name;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('physical_store_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}
