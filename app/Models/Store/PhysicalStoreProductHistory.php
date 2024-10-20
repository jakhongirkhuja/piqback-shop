<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helper\StandardAttributes;
class PhysicalStoreProductHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->category_id = $model->category_id;
        $this->store_id = $model->store_id;
        $this->name =$model->name;
        $this->img= $model->img;
        $this->price = $model->price;
        $this->amount = $model->amount;
        $this->ordered = $model->ordered;
        $this->description = $model->description;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('digital_store_product_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}
