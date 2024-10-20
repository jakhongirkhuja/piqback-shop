<?php

namespace App\Models\StoreLatest;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerTelegramHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($model, $data, $status)
    {
        $this->telegram_id = $model->telegram_id;
        $this->seller_id = $model->seller_id;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('seller_telegram_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
    public function saveModelBot($model, $data, $status, $userId)
    {
        $this->telegram_id = $model->telegram_id;
        $this->seller_id = $model->seller_id;
        $this->moderated = $userId;
        $this->save();
        StandardAttributes::setSA('seller_telegram_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql8');
    }
}
