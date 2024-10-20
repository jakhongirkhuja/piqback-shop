<?php

namespace App\Models\Store;
use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalStoreHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->category_id = $model->category_id;
        $this->name = $model->name;
        $this->logo =$model->logo;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('digital_store_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}

