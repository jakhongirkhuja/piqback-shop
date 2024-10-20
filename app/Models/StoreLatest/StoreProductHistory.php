<?php

namespace App\Models\StoreLatest;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreProductHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->store_id = $model->store_id;
        $this->category_id = $model->category_id;
        $this->productName = $model->productName;
        $this->productDescription = $model->productDescription;
        $this->productCost =$model->productCost;
        $this->productAmount= $model->productAmount;
        $this->productIMG = $model->productIMG;
        $this->iqc =$model->iqc;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('store_product_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}
