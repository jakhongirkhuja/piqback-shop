<?php

namespace App\Models\StoreLatest;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSellerHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->sellerName = $model->sellerName;
        $this->sellerPhone = $model->sellerPhone;
        $this->role = $model->role;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('store_seller_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
    public function saveModelBot($model, $data, $status, $userid)
    {
        $this->sellerName = $model->sellerName;
        $this->sellerPhone = $model->sellerPhone;
        $this->role = $model->role;
        $this->moderated = $userid;
        $this->save();
        StandardAttributes::setSA('store_seller_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}
